<?php

namespace App\Http\Controllers\Admin;

use App\PromotionalCampaign;
use App\Services\CronMonitorService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class CronMonitorController extends MainAdminController
{
    protected $cronMonitor;

    public function __construct()
    {
        $this->middleware('auth');

        parent::__construct();
        $this->cronMonitor = new CronMonitorService();
    }

    protected function ensureAdminAccess()
    {
        if (Auth::User()->usertype != 'Admin' && Auth::User()->usertype != 'Sub_Admin') {
            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        $page_title = 'Cron Monitor';
        $status = $this->cronMonitor->getStatus();
        $projectPath = base_path();
        $phpBinary = 'php';
        $scheduleCommand = '* * * * * cd "'.$projectPath.'" && '.$phpBinary.' artisan schedule:run >> /dev/null 2>&1';
        $taskCommand = '* * * * * cd "'.$projectPath.'" && '.$phpBinary.' artisan task:cron >> /dev/null 2>&1';
        $triggerUrl = URL::to('cron/task-run/'.$this->cronMonitor->getTriggerToken());

        $campaignStats = [
            'scheduled_total' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_SCHEDULED)->count(),
            'scheduled_due' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_SCHEDULED)
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<=', now())
                ->count(),
            'running_total' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_RUNNING)->count(),
            'failed_total' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_FAILED)->count(),
            'completed_today' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_COMPLETED)
                ->whereDate('completed_at', now()->toDateString())
                ->count(),
        ];

        $latestCampaigns = PromotionalCampaign::with('user')
            ->orderByRaw("CASE WHEN status = 'scheduled' THEN 0 WHEN status = 'running' THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('admin.pages.cron.monitor', compact(
            'page_title',
            'status',
            'projectPath',
            'scheduleCommand',
            'taskCommand',
            'triggerUrl',
            'campaignStats',
            'latestCampaigns'
        ));
    }

    public function runNow()
    {
        if ($redirect = $this->ensureAdminAccess()) {
            return $redirect;
        }

        try {
            app()->instance('cron.run_trigger', 'admin_panel');
            $exitCode = Artisan::call('task:cron');

            if ((int) $exitCode === 0) {
                \Session::flash('flash_message', 'Cron command executed successfully.');
            } else {
                $output = trim(Artisan::output());
                \Session::flash('error_flash_message', $output ?: 'Cron command failed. Check logs for details.');
            }
        } catch (\Throwable $exception) {
            \Session::flash('error_flash_message', $exception->getMessage());
        }

        return redirect('admin/cron_monitor');
    }

    public function trigger($token)
    {
        if (!hash_equals($this->cronMonitor->getTriggerToken(), (string) $token)) {
            abort(403);
        }

        try {
            app()->instance('cron.run_trigger', 'http_trigger');
            $exitCode = Artisan::call('task:cron');

            if ((int) $exitCode !== 0) {
                $output = trim(Artisan::output());

                return response()->json([
                    'status' => 'failed',
                    'message' => $output ?: 'Cron command failed. Check logs for details.',
                    'time' => now()->toDateTimeString(),
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cron executed successfully.',
                'time' => now()->toDateTimeString(),
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'status' => 'failed',
                'message' => $exception->getMessage(),
                'time' => now()->toDateTimeString(),
            ], 500);
        }
    }
}
