<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class CronMonitorService
{
    protected $statusPath;

    public function __construct()
    {
        $this->statusPath = storage_path('app/cron_monitor_status.json');
    }

    public function getStatus()
    {
        if (!File::exists($this->statusPath)) {
            return $this->defaultStatus();
        }

        $decoded = json_decode(File::get($this->statusPath), true);

        return is_array($decoded) ? array_merge($this->defaultStatus(), $decoded) : $this->defaultStatus();
    }

    public function markStart($trigger = 'scheduler')
    {
        $status = $this->getStatus();
        $status['last_trigger'] = $trigger;
        $status['last_started_at'] = now()->toDateTimeString();
        $status['last_finished_at'] = null;
        $status['last_status'] = 'running';
        $status['last_message'] = 'Cron command started.';
        $status['last_duration_seconds'] = null;

        $this->writeStatus($status);
    }

    public function markSuccess($message = 'Cron completed successfully.', array $extra = [])
    {
        $status = array_merge($this->getStatus(), $extra);
        $status['last_finished_at'] = now()->toDateTimeString();
        $status['last_status'] = 'success';
        $status['last_message'] = $message;
        $status['last_duration_seconds'] = $this->calculateDuration(
            $status['last_started_at'] ?? null,
            $status['last_finished_at']
        );

        $this->writeStatus($status);
    }

    public function markFailure($message, array $extra = [])
    {
        $status = array_merge($this->getStatus(), $extra);
        $status['last_finished_at'] = now()->toDateTimeString();
        $status['last_status'] = 'failed';
        $status['last_message'] = $message;
        $status['last_duration_seconds'] = $this->calculateDuration(
            $status['last_started_at'] ?? null,
            $status['last_finished_at']
        );

        $this->writeStatus($status);
    }

    public function getTriggerToken()
    {
        return hash_hmac('sha256', 'task-cron-trigger', (string) config('app.key'));
    }

    protected function writeStatus(array $status)
    {
        File::ensureDirectoryExists(dirname($this->statusPath));
        File::put($this->statusPath, json_encode($status, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    protected function calculateDuration($start, $finish)
    {
        if (empty($start) || empty($finish)) {
            return null;
        }

        return max(0, strtotime($finish) - strtotime($start));
    }

    protected function defaultStatus()
    {
        return [
            'last_trigger' => null,
            'last_started_at' => null,
            'last_finished_at' => null,
            'last_status' => 'never',
            'last_message' => 'Cron has not run yet.',
            'last_duration_seconds' => null,
            'campaigns_checked' => 0,
            'running_campaigns_seen' => 0,
            'scheduled_campaigns_seen' => 0,
        ];
    }
}
