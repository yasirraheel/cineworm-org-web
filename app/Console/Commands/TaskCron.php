<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use App\PromotionalCampaign;
use App\Services\PromotionalCampaignService;
use App\Services\CronMonitorService;
use App\Services\WhatsappCampaignService;
use App\Services\WhatsappServerService;
use App\WhatsappCampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;



class TaskCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notification Email sending';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $trigger = app()->bound('cron.run_trigger') ? app('cron.run_trigger') : 'scheduler';
        $monitor = new CronMonitorService();
        $monitor->markStart($trigger);
        $expiryEmailFailures = 0;

        try {
            \Log::info("Cron is working fine!");

            $TodayDate=strtotime(date('Y-m-d'));

            $users = DB::table('users')
                ->where('status',1)
                ->where('exp_date','=',$TodayDate)
                ->get();

            foreach ($users as $user_data) {
                $expiryMailKey = 'task_cron_expiry_email_sent:' . ($user_data->id ?? 'unknown') . ':' . date('Y-m-d');

                if (Cache::has($expiryMailKey)) {
                    continue;
                }

                try {
                    Mail::to($user_data->email)->send(new SendEmail($user_data));
                    Cache::put($expiryMailKey, true, now()->endOfDay());
                } catch (\Throwable $exception) {
                    Cache::put($expiryMailKey, true, now()->endOfDay());
                    $expiryEmailFailures++;
                    \Log::warning('Task cron expiry email failed: '.$exception->getMessage(), [
                        'user_id' => $user_data->id ?? null,
                        'email' => $user_data->email ?? null,
                    ]);
                }
            }

            (new PromotionalCampaignService())->processDueCampaigns(5, 25);
            (new WhatsappServerService())->ensureRunning();
            (new WhatsappCampaignService())->processDueCampaigns(3, 10);

            $monitor->markSuccess('Cron completed successfully.', [
                'campaigns_checked' => PromotionalCampaign::whereIn('status', [
                    PromotionalCampaign::STATUS_SCHEDULED,
                    PromotionalCampaign::STATUS_RUNNING,
                ])->count(),
                'running_campaigns_seen' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_RUNNING)->count(),
                'scheduled_campaigns_seen' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_SCHEDULED)->count(),
                'whatsapp_campaigns_checked' => WhatsappCampaign::whereIn('status', [
                    WhatsappCampaign::STATUS_SCHEDULED,
                    WhatsappCampaign::STATUS_RUNNING,
                ])->count(),
                'expiry_email_failures' => $expiryEmailFailures,
            ]);

            $this->info('Demo:Cron Cummand Run successfully!');

            return 0;
        } catch (\Throwable $exception) {
            \Log::error('Task cron failed: '.$exception->getMessage());
            $monitor->markFailure($exception->getMessage(), [
                'running_campaigns_seen' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_RUNNING)->count(),
                'scheduled_campaigns_seen' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_SCHEDULED)->count(),
            ]);
            $this->error($exception->getMessage());

            return 1;
        }
    }
}
