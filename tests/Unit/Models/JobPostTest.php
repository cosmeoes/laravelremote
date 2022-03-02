<?php

namespace Tests\Unit\Models;

use App\Models\JobPost;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class JobPostTest extends TestCase
{
    use DatabaseMigrations;

    public function test_has_tag()
    {
        $tag = Tag::factory()->create();
        $jobPost = JobPost::factory()->create();

        $this->assertFalse($jobPost->hasTag($tag));

        $jobPost->tags()->save($tag);

        $this->assertTrue($jobPost->hasTag($tag));
    }


    public function test_add_tag()
    {
        $tag = Tag::factory()->create();
        $jobPost = JobPost::factory()->create();
        $jobPost->addTag($tag);
        $this->assertTrue($jobPost->hasTag($tag));

        $jobPost->addTag($tag);
        $this->assertTrue($jobPost->hasTag($tag));
    }
}
