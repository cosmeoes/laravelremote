<?php

namespace App\Console\Commands;

use App\Helpers\Tagger;
use App\Models\JobPost;
use Illuminate\Console\Command;

class TagNewPosts extends Command
{
    protected $signature = 'lr:tag-new-posts {--hours=24} {--all}';

    protected $description = 'Tags post created in the given timeframe';

    public function handle()
    {
        $jobs = $this->jobs();

        foreach ($jobs as $job) {
            $this->tag($job);
        }

        return 0;
    }

    public function jobs()
    {
        if ($this->option('all')) {
            return JobPost::all();
        }

        $timestamp = now()->subHours($this->option('hours'));

        return JobPost::where('created_at', '<=', $timestamp)->get();
    }

    public function tag($job) 
    {
        Tagger::tag($job);
    }
}
