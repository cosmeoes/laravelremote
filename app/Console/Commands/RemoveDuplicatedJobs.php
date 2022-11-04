<?php

namespace App\Console\Commands;

use App\Models\JobPost;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RemoveDuplicatedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lr:remove-duplicated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes duplicated jobs by position and company';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $duplicated = $this->getDuplicated();

        $removed = 0;
        foreach($duplicated as $d) {
            $removed += $this->onlyOne($d);
        }

        Log::info("Removed $removed duplicated jobs");
        return 0;
    }

    public function getDuplicated()
    {
        return JobPost::selectRaw('count(*) count, position, company')
            ->groupBy('position', 'company')
            ->having('count', '>=', 2)
            ->get();
    }

    public function onlyOne($duplicated)
    {
        $jobs = JobPost::query()->where('position', $duplicated->position)->where('company', $duplicated->company)->get();

        $deleted = 0;
        $jobs->skip(1)->each(function ($job) use (&$deleted) {
            $deleted++;
            $job->tags()->detach();
            $job->delete();
        });

        return $deleted;
    }
}
