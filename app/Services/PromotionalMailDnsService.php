<?php

namespace App\Services;

use App\PromotionalSendingDomain;
use Exception;
use Illuminate\Support\Facades\Log;

class PromotionalMailDnsService
{
    public function initializeDomain(PromotionalSendingDomain $domain): PromotionalSendingDomain
    {
        if (empty($domain->selector)) {
            $domain->selector = 'xsender';
        }

        if (empty($domain->return_path_subdomain)) {
            $domain->return_path_subdomain = 'mail';
        }

        if (empty($domain->dmarc_policy)) {
            $domain->dmarc_policy = 'quarantine';
        }

        if (empty($domain->dmarc_alignment)) {
            $domain->dmarc_alignment = 'relaxed';
        }

        if (!$domain->isDkimConfigured()) {
            $this->generateKeyPair($domain, $domain->selector);
        } else {
            $this->refreshDnsRecords($domain);
        }

        return $domain->fresh();
    }

    public function generateKeyPair(PromotionalSendingDomain $domain, $selector = 'xsender'): PromotionalSendingDomain
    {
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $opensslCnf = $this->findOpensslConfig();
        if ($opensslCnf) {
            $config['config'] = $opensslCnf;
        }

        $keyPair = openssl_pkey_new($config);
        if (!$keyPair) {
            $error = '';
            while ($msg = openssl_error_string()) {
                $error .= $msg . ' ';
            }

            Log::error('Promotional DKIM key generation failed: ' . trim($error));
            throw new Exception('Failed to generate DKIM key pair' . ($error ? ': ' . trim($error) : ''));
        }

        openssl_pkey_export($keyPair, $privateKey, null, $config);
        $publicKeyDetails = openssl_pkey_get_details($keyPair);
        $publicKey = $publicKeyDetails['key'];

        $domain->selector = $selector;
        $domain->dkim_private_key = $privateKey;
        $domain->dkim_public_key = $publicKey;
        $domain->dkim_status = 0;
        $domain->spf_status = 0;
        $domain->dmarc_status = 0;
        $domain->verified_at = null;
        $domain->save();

        $this->refreshDnsRecords($domain);

        return $domain->fresh();
    }

    public function refreshDnsRecords(PromotionalSendingDomain $domain): PromotionalSendingDomain
    {
        $publicKeyForDns = $this->cleanPublicKeyForDns($domain->dkim_public_key ?: '');
        $domain->dkim_value = $publicKeyForDns ? 'v=DKIM1; k=rsa; p=' . $publicKeyForDns : null;
        $domain->spf_value = $this->buildSpfRecord($domain);
        $domain->save();

        return $domain->fresh();
    }

    public function verifyDns(PromotionalSendingDomain $domain): array
    {
        $messages = [];
        $dkim = $this->verifyDkim($domain, $messages);
        $spf = $this->verifySpf($domain, $messages);
        $dmarc = $this->verifyDmarc($domain, $messages);

        $domain->dkim_status = $dkim ? 1 : 0;
        $domain->spf_status = $spf ? 1 : 0;
        $domain->dmarc_status = $dmarc ? 1 : 0;
        $domain->dns_checked_at = now();
        $domain->verified_at = ($dkim && $spf && $dmarc) ? ($domain->verified_at ?: now()) : null;
        $domain->status = $dkim ? 1 : 0;
        $domain->save();

        return [
            'dkim' => $dkim,
            'spf' => $spf,
            'dmarc' => $dmarc,
            'messages' => $messages,
        ];
    }

    public function getDnsRecords(PromotionalSendingDomain $domain): array
    {
        return [
            'dkim' => [
                'type' => $domain->dkim_type ?: 'TXT',
                'hostname' => $domain->selector . '._domainkey.' . $domain->domain,
                'value' => $domain->dkim_value,
                'verified' => (bool) $domain->dkim_status,
            ],
            'spf' => [
                'type' => 'TXT',
                'hostname' => $domain->domain,
                'value' => $this->buildSpfRecord($domain),
                'verified' => (bool) $domain->spf_status,
            ],
            'dmarc' => [
                'type' => 'TXT',
                'hostname' => '_dmarc.' . $domain->domain,
                'value' => $this->buildDmarcRecord($domain),
                'verified' => (bool) $domain->dmarc_status,
            ],
        ];
    }

    public function checkOpenSslReadiness(): array
    {
        $result = [
            'ready' => false,
            'openssl_loaded' => extension_loaded('openssl'),
            'openssl_version' => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : null,
            'config_path' => null,
            'error' => null,
        ];

        if (!$result['openssl_loaded']) {
            $result['error'] = 'The OpenSSL PHP extension is not installed or enabled on your server.';
            return $result;
        }

        $configPath = $this->findOpensslConfig();
        $result['config_path'] = $configPath;

        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        if ($configPath) {
            $config['config'] = $configPath;
        }

        $key = @openssl_pkey_new($config);
        if ($key) {
            $result['ready'] = true;
        } else {
            $error = '';
            while ($msg = openssl_error_string()) {
                $error .= $msg . ' ';
            }
            $result['error'] = trim($error) ?: 'OpenSSL failed to generate a test key. The openssl.cnf configuration file may be missing.';
        }

        return $result;
    }

    protected function verifyDkim(PromotionalSendingDomain $domain, array &$messages): bool
    {
        if (!$domain->isDkimConfigured() || empty($domain->dkim_value)) {
            $messages[] = 'DKIM keys are not generated yet.';
            return false;
        }

        $hostname = $domain->selector . '._domainkey.' . $domain->domain;
        $expectedPublicKey = $this->cleanPublicKeyForDns($domain->dkim_public_key);
        $expectedRecord = $this->normalizeDnsText((string) $domain->dkim_value);

        try {
            $records = $this->lookupTxtRecords($hostname);
            if (empty($records)) {
                $messages[] = 'No DKIM TXT record found at ' . $hostname . '.';
                return false;
            }

            foreach ($records as $txt) {
                $normalizedTxt = $this->normalizeDnsText($txt);
                $normalizedKey = $this->normalizeDnsText($expectedPublicKey);

                if ($expectedRecord && $normalizedTxt === $expectedRecord) {
                    $messages[] = 'DKIM record verified successfully.';
                    return true;
                }

                if ($normalizedKey && strpos($normalizedTxt, $normalizedKey) !== false) {
                    $messages[] = 'DKIM record verified successfully.';
                    return true;
                }
            }

            foreach ($records as $txt) {
                $normalizedTxt = $this->normalizeDnsText($txt);

                if (strpos($normalizedTxt, 'v=dkim1') !== false && strpos($normalizedTxt, 'p=') !== false) {
                    $messages[] = 'DKIM TXT record was found on the server. Marking it verified because the record exists, even though formatting or key content does not exactly match the generated value.';
                    return true;
                }
            }

            $messages[] = 'DKIM record was found, but it does not look like a usable DKIM TXT record.';
        } catch (\Throwable $exception) {
            Log::warning('Promotional DKIM verification failed for ' . $domain->domain . ': ' . $exception->getMessage());
            $messages[] = 'DNS lookup failed for the DKIM record.';
        }

        return false;
    }

    protected function verifySpf(PromotionalSendingDomain $domain, array &$messages): bool
    {
        try {
            $records = $this->lookupTxtRecords($domain->domain);
            if (empty($records)) {
                $messages[] = 'No TXT records found for SPF.';
                return false;
            }

            foreach ($records as $txt) {
                $normalizedTxt = $this->normalizeDnsText($txt);
                if (stripos($normalizedTxt, 'v=spf1') === 0) {
                    $messages[] = 'SPF record found.';
                    return true;
                }
            }

            $messages[] = 'No SPF record found.';
        } catch (\Throwable $exception) {
            $messages[] = 'DNS lookup failed for the SPF record.';
        }

        return false;
    }

    protected function verifyDmarc(PromotionalSendingDomain $domain, array &$messages): bool
    {
        $hostname = '_dmarc.' . $domain->domain;

        try {
            $records = $this->lookupTxtRecords($hostname);
            if (empty($records)) {
                $messages[] = 'No DMARC record found.';
                return false;
            }

            foreach ($records as $txt) {
                $normalizedTxt = $this->normalizeDnsText($txt);
                if (stripos($normalizedTxt, 'v=dmarc1') === 0) {
                    $messages[] = 'DMARC record found.';
                    return true;
                }
            }

            $messages[] = 'No DMARC record found.';
        } catch (\Throwable $exception) {
            $messages[] = 'DNS lookup failed for the DMARC record.';
        }

        return false;
    }

    protected function buildSpfRecord(PromotionalSendingDomain $domain): string
    {
        if (!empty($domain->spf_value)) {
            return $domain->spf_value;
        }

        return 'v=spf1 include:_spf.' . $domain->domain . ' ~all';
    }

    protected function buildDmarcRecord(PromotionalSendingDomain $domain): string
    {
        $alignment = $domain->dmarc_alignment === 'strict' ? 's' : 'r';
        $record = 'v=DMARC1; p=' . ($domain->dmarc_policy ?: 'quarantine') . '; adkim=' . $alignment . '; aspf=' . $alignment;

        if (!empty($domain->dmarc_report_email)) {
            $record .= '; rua=mailto:' . $domain->dmarc_report_email;
        }

        return $record;
    }

    protected function cleanPublicKeyForDns(string $publicKey): string
    {
        return str_replace(
            ["\n", "\r", '-----BEGIN PUBLIC KEY-----', '-----END PUBLIC KEY-----'],
            '',
            $publicKey
        );
    }

    protected function normalizeDnsText(string $value): string
    {
        return strtolower(str_replace(['" "', '"', "\t", "\n", "\r", ' '], '', trim($value)));
    }

    protected function lookupTxtRecords(string $hostname): array
    {
        $publicLookup = $this->lookupTxtRecordsViaPublicDns($hostname);
        if ($publicLookup['resolved']) {
            return $publicLookup['records'];
        }

        $records = [];

        try {
            $dnsRecords = @dns_get_record($hostname, DNS_TXT);
            if (is_array($dnsRecords)) {
                foreach ($dnsRecords as $record) {
                    if (!empty($record['txt'])) {
                        $records[] = $record['txt'];
                    } elseif (!empty($record['entries']) && is_array($record['entries'])) {
                        $records[] = implode('', $record['entries']);
                    }
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('dns_get_record TXT lookup failed for ' . $hostname . ': ' . $exception->getMessage());
        }

        if (!empty($records)) {
            return $records;
        }

        $shellRecords = $this->lookupTxtRecordsViaShell($hostname);

        return !empty($shellRecords) ? $shellRecords : [];
    }

    protected function lookupTxtRecordsViaPublicDns(string $hostname): array
    {
        $endpoints = [
            [
                'url' => 'https://cloudflare-dns.com/dns-query?name=' . rawurlencode($hostname) . '&type=TXT',
                'headers' => [
                    'accept: application/dns-json',
                ],
            ],
            [
                'url' => 'https://dns.google/resolve?name=' . rawurlencode($hostname) . '&type=TXT',
                'headers' => [],
            ],
        ];

        foreach ($endpoints as $endpoint) {
            $body = $this->fetchUrl($endpoint['url'], $endpoint['headers']);
            if ($body === null || $body === '') {
                continue;
            }

            $json = json_decode($body, true);
            if (!is_array($json) || !array_key_exists('Status', $json)) {
                continue;
            }

            if ((int) $json['Status'] !== 0) {
                return [
                    'resolved' => true,
                    'records' => [],
                ];
            }

            $records = [];
            foreach (($json['Answer'] ?? []) as $answer) {
                if ((int) ($answer['type'] ?? 0) !== 16 || empty($answer['data'])) {
                    continue;
                }

                $records[] = trim((string) $answer['data'], '"');
            }

            return [
                'resolved' => true,
                'records' => $records,
            ];
        }

        return [
            'resolved' => false,
            'records' => [],
        ];
    }

    protected function lookupTxtRecordsViaShell(string $hostname): array
    {
        $records = [];
        $commands = [];

        if (stripos(PHP_OS, 'WIN') === 0) {
            $commands[] = 'nslookup -type=TXT ' . escapeshellarg($hostname);
        } else {
            $commands[] = 'dig +short TXT ' . escapeshellarg($hostname);
            $commands[] = 'nslookup -type=TXT ' . escapeshellarg($hostname);
        }

        foreach ($commands as $command) {
            $output = $this->runShellCommand($command);
            if (empty($output)) {
                continue;
            }

            foreach (preg_split("/\r\n|\n|\r/", $output) as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                if (stripos($line, 'text =') !== false) {
                    $line = trim(substr($line, strpos($line, '=') + 1));
                }

                if (preg_match('/^".*"$/', $line)) {
                    $line = trim($line, '"');
                }

                if (stripos($line, 'v=') !== false || stripos($line, 'k=rsa') !== false || stripos($line, 'p=') !== false) {
                    $records[] = str_replace('" "', '', $line);
                }
            }

            if (!empty($records)) {
                break;
            }
        }

        return $records;
    }

    protected function runShellCommand(string $command): string
    {
        if (!function_exists('shell_exec')) {
            return '';
        }

        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('shell_exec', $disabled, true)) {
            return '';
        }

        try {
            return (string) @shell_exec($command . ' 2>&1');
        } catch (\Throwable $exception) {
            return '';
        }
    }

    protected function fetchUrl(string $url, array $headers = []): ?string
    {
        if (function_exists('curl_init')) {
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                if (!empty($headers)) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }

                $response = curl_exec($ch);
                $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($response !== false && $httpCode >= 200 && $httpCode < 500) {
                    return (string) $response;
                }
            } catch (\Throwable $exception) {
            }
        }

        if (ini_get('allow_url_fopen')) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'timeout' => 20,
                        'header' => implode("\r\n", $headers),
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);

                $response = @file_get_contents($url, false, $context);
                if ($response !== false) {
                    return (string) $response;
                }
            } catch (\Throwable $exception) {
            }
        }

        return null;
    }

    protected function findOpensslConfig(): ?string
    {
        $candidates = [
            getenv('OPENSSL_CONF') ?: '',
            dirname(PHP_BINARY) . '/extras/ssl/openssl.cnf',
            dirname(PHP_BINARY) . '/../extras/ssl/openssl.cnf',
        ];

        $phpIni = php_ini_loaded_file();
        if ($phpIni) {
            $phpDir = dirname($phpIni);
            $candidates[] = $phpDir . '/extras/ssl/openssl.cnf';
            $candidates[] = dirname($phpDir) . '/extras/ssl/openssl.cnf';
        }

        foreach ($candidates as $path) {
            if ($path && file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
