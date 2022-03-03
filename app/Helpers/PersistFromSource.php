<?php

namespace App\Helpers;

use App\Models\JobPost;
use Illuminate\Support\Arr;

class PersistFromSource
{
    public $locationParser;

    public function __construct(LocationParser $locationParser)
    {
        $this->locationParser = $locationParser;
    }

    public function do($jobs)
    {
        foreach ($jobs as $job) {
            if (self::shouldAddJob($job)) {
                $job = $this->addMissingFields($job);
                JobPost::create($job);
            }
        }
    }

    private function shouldAddJob($job)
    {
        return JobPost::where('source_url', $job['source_url'])->orWhere('apply_url', $job['apply_url'])->orWhere(function($query) use ($job) {
            $query->where('position', $job['position'])
                  ->where('company', $job['company'])
                  ->where('created_at', '>=', now()->addWeek());
        })->doesntExist();
    }

    private function addMissingFields($job)
    {
        if (Arr::get($job, 'location') == null) {
            $job['location'] = $this->locationParser->parse($job['body']);
        }

        return $job;
    }
}

