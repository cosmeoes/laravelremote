<?php

namespace App\Console\Commands;

use App\Models\JobPost;
use App\Services\Mailchimp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDailyJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lr:send-daily-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends the daily jobs to the users subscribed to the "daily" list';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Mailchimp $mailchimp)
    {
        $jobs = JobPost::whereRaw("datetime(created_at) >= datetime('" . now()->subDay()->toDateTimeString() . "')")->get();
        if ($jobs->isEmpty()) {
            Log::warning('Not sending daily, no new jobs');
        }

        $mailchimp->sendEmail(view('email.new-jobs-notification', ['jobs' => $jobs])->render());
        return 0;
    }
}
