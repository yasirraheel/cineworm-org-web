<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappServerService
{
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(3)
                ->acceptJson()
                ->get(rtrim(config('whatsapp.server_url'), '/') . '/health');

            return $response->successful() && (bool) $response->json('ok');
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public function ensureRunning(): bool
    {
        if ($this->isHealthy()) {
            return true;
        }

        $output = $this->runStartCommand();

        if (!$this->isHealthy()) {
            Log::warning('WhatsApp server self-heal did not restore health.', [
                'output' => $output,
            ]);

            return false;
        }

        Log::info('WhatsApp server self-heal started the sidecar.', [
            'output' => $output,
        ]);

        return true;
    }

    protected function runStartCommand(): string
    {
        $serverPath = config('whatsapp.server_path');
        $pm2Name = config('whatsapp.pm2_name');
        $home = getenv('HOME') ?: null;

        if (!$home && preg_match('#^(/home/[^/]+)#', base_path(), $matches)) {
            $home = $matches[1];
        }

        $home = $home ?: dirname(base_path(), 4);
        $path = $home . '/opt/node/bin:' . $home . '/bin:/usr/local/bin:/usr/bin:/bin:$PATH';

        $command = sprintf(
            'cd %s && export PATH=%s && (pm2 describe %s >/dev/null 2>&1 && pm2 restart %s --update-env || pm2 start index.js --name %s --time) && pm2 save 2>&1',
            escapeshellarg($serverPath),
            escapeshellarg($path),
            escapeshellarg($pm2Name),
            escapeshellarg($pm2Name),
            escapeshellarg($pm2Name)
        );

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = \proc_open(['/bin/bash', '-lc', $command], $descriptors, $pipes);

        if (!\is_resource($process)) {
            return 'Unable to start shell process.';
        }

        \fclose($pipes[0]);
        $output = \stream_get_contents($pipes[1]);
        $errorOutput = \stream_get_contents($pipes[2]);
        \fclose($pipes[1]);
        \fclose($pipes[2]);
        $exitCode = \proc_close($process);

        return trim($output . PHP_EOL . $errorOutput . PHP_EOL . 'exit_code=' . $exitCode);
    }
}
