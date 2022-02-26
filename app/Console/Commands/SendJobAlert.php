<?php

namespace App\Console\Commands;

use App\Models\JobPost;
use App\Services\Mailchimp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

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

        $mailchimp->sendEmail(view('email.new-jobs-notification', ['jobs' => $jobs])->render(), $this->subject(), $this->segmentId());
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

    public function subject()
    {
        return match($this->option('type')) {
            'daily' => "Today's Laravel Remote Jobs",
            'weekly' => "This week's Laravel Remote Jobs",
            default => throw new Exception("I couldn't figure out the subject for type: " . $this->option('type'))
        };
    }

    public function segmentId()
    {
        return match($this->option('type')) {
            'daily' => intval(config('services.mailchimp.daily_segment_id')),
            'weekly' => intval(config('services.mailchimp.weekly_segment_id')),
            default => throw new Exception("I couldn't figure out the subject for type: " . $this->option('type'))
        };
    }
}
