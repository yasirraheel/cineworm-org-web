<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use App\PromotionalCampaign;
use App\Services\PromotionalCampaignService;
use App\Services\CronMonitorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
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
                try {
                    Mail::to($user_data->email)->send(new SendEmail($user_data));
                } catch (\Throwable $exception) {
                    $expiryEmailFailures++;
                    \Log::warning('Task cron expiry email failed: '.$exception->getMessage(), [
                        'user_id' => $user_data->id ?? null,
                        'email' => $user_data->email ?? null,
                    ]);
                }
            }

            (new PromotionalCampaignService())->processDueCampaigns(5, 25);

            $monitor->markSuccess('Cron completed successfully.', [
                'campaigns_checked' => PromotionalCampaign::whereIn('status', [
                    PromotionalCampaign::STATUS_SCHEDULED,
                    PromotionalCampaign::STATUS_RUNNING,
                ])->count(),
                'running_campaigns_seen' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_RUNNING)->count(),
                'scheduled_campaigns_seen' => PromotionalCampaign::where('status', PromotionalCampaign::STATUS_SCHEDULED)->count(),
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
