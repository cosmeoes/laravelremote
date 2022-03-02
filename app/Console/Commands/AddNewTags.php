<?php

namespace App\Console\Commands;

use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AddNewTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lr:add-new-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds new tags to the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $created = 0;
        foreach (config('tags') as $slug => $tag) {
            $tag = Tag::query()->firstOrCreate([
                'slug' => $slug,
                'name' => $tag['name']
            ]);

            if ($tag->wasRecentlyCreated) {
                $created++;
            }
        }

        Log::info("Added $created new tags");
        return 0;
    }
}
