<?php

namespace Tests\Feature;

use App\Console\Commands\AddNewTags;
use App\Console\Commands\TagNewPosts;
use App\Models\JobPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagNewPostTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() : void
    {
        parent::setUp();
        $this->artisan(AddNewTags::class);
    }

    public function test_tags_all_posts()
    {
        $jobA = JobPost::factory()->taggable()->create();
        $jobB = JobPost::factory()->taggable()->create(['created_at' => now()->subHours(2)]);
        $jobC = JobPost::factory()->taggable()->create(['created_at' => now()->subHours(25)]);

        $this->artisan(TagNewPosts::class, ['--all' => true]);

        $this->assertNotEmpty($jobA->tags);
        $this->assertNotEmpty($jobB->tags);
        $this->assertNotEmpty($jobC->tags);
    }

    public function test_tags_posts_created_in_the_last_24_hours()
    {
        $jobA = JobPost::factory()->taggable()->create();
        $jobB = JobPost::factory()->taggable()->create(['created_at' => now()->subHours(2)]);
        $jobC = JobPost::factory()->taggable()->create(['created_at' => now()->subHours(25)]);

        $this->artisan(TagNewPosts::class);

        $this->assertNotEmpty($jobA->tags);
        $this->assertNotEmpty($jobB->tags);
        $this->assertEmpty($jobC->tags);
    }
}
