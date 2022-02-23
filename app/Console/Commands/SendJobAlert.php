<?php

namespace App\Console\Commands;

use App\Models\JobPost;
use App\Services\Mailchimp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendJobAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lr:send-job-alert {--type=}';

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
        $this->validate();
        $jobs = $this->jobs();
        if ($jobs->isEmpty()) {
            Log::warning('Not sending daily, no new jobs');
        }

        $mailchimp->sendEmail(view('email.new-jobs-notification', ['jobs' => $jobs])->render());
        return 0;
    }

    public function jobs()
    {
        return JobPost::whereRaw("datetime(created_at) >= datetime('" . $this->timestamp()->toDateTimeString() . "')")->get();
    }

    public function timestamp()
    {
        $timestamp = now();
        if ($this->option('type') == 'daily') {
            $timestamp->subDay();
        } else if ($this->option('type') == 'weekly') {
            $timestamp->subWeek();
        }

        return $timestamp;
    }

    public function validate()
    {
        if (!in_array($this->option('type'), ['weekly', 'daily'])) {
            Log::info('Type must be weekly or daily');
            exit(1);
        }
    }
}
