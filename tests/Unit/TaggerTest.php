<?php

namespace Tests\Unit;

use App\Console\Commands\AddNewTags;
use App\Helpers\Tagger;
use App\Models\JobPost;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class TaggerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() : void
    {
        parent::setUp();
        Artisan::call(AddNewTags::class);
    }

    public function test_adds_javascript_tag()
    {
        $job = JobPost::factory()->createOne([
            'body' => "For this position we are looking for js developers",
        ]);

        Tagger::tag($job);
        $this->assertContains('javascript', $job->tags->map->name);
    }


    public function test_matches_tag()
    {
        $this->assertTrue(Tagger::matchesTag('lemayo javascript lel', config('tags')['javascript']));
    }
}
