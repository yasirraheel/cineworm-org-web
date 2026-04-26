<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use App\Services\PromotionalCampaignService;
use App\User;
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
        \Log::info("Cron is working fine!");

        $TodayDate=strtotime(date('Y-m-d'));

        $users = DB::table('users')
            ->where('status',1)
            ->where('exp_date','=',$TodayDate)
            ->get();

        foreach ($users as $user_data) {
            Mail::to($user_data->email)->send(new SendEmail($user_data));
        }

        try {
            (new PromotionalCampaignService())->processDueCampaigns(5, 25);
        } catch (\Throwable $exception) {
            \Log::error('Promotional campaign cron failed: '.$exception->getMessage());
        }

        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
