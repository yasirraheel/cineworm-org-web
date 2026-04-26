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

        try {
            $records = dns_get_record($hostname, DNS_TXT);
            if (empty($records)) {
                $messages[] = 'No DKIM TXT record found at ' . $hostname . '.';
                return false;
            }

            foreach ($records as $record) {
                $txt = $record['txt'] ?? '';
                $txt = str_replace(['" "', "\t", "\n", "\r", ' '], '', $txt);

                if ($expectedPublicKey && strpos($txt, $expectedPublicKey) !== false) {
                    $messages[] = 'DKIM record verified successfully.';
                    return true;
                }
            }

            $messages[] = 'DKIM record found but the public key does not match.';
        } catch (\Throwable $exception) {
            Log::warning('Promotional DKIM verification failed for ' . $domain->domain . ': ' . $exception->getMessage());
            $messages[] = 'DNS lookup failed for the DKIM record.';
        }

        return false;
    }

    protected function verifySpf(PromotionalSendingDomain $domain, array &$messages): bool
    {
        try {
            $records = dns_get_record($domain->domain, DNS_TXT);
            if (empty($records)) {
                $messages[] = 'No TXT records found for SPF.';
                return false;
            }

            foreach ($records as $record) {
                $txt = $record['txt'] ?? '';
                if (stripos($txt, 'v=spf1') === 0) {
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
            $records = dns_get_record($hostname, DNS_TXT);
            if (empty($records)) {
                $messages[] = 'No DMARC record found.';
                return false;
            }

            foreach ($records as $record) {
                $txt = $record['txt'] ?? '';
                if (stripos($txt, 'v=DMARC1') === 0) {
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
