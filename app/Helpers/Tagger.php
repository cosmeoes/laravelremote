<?php

namespace App\Helpers;

use App\Models\JobPost;
use App\Models\Tag;
use Illuminate\Support\Str;

class Tagger 
{
    public static function tag(JobPost $job)
    {
        foreach (config('tags') as $slug => $tag) {
            if (self::matchesTag($job->body, $tag)) {
                $tag = Tag::where('slug', $slug)->first();
                $job->addTag($tag);
            }
        }
    }

    public static function matchesTag($text, $tag)
    {
        return Str::contains($text, $tag['matches'], true);
    }
}
