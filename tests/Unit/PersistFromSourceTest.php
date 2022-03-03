<?php

namespace Tests\Unit;

use App\Helpers\PersistFromSource;
use App\Models\JobPost;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PersistFromSourceTest extends TestCase
{
    use DatabaseMigrations;

    public function test_stores_array_to_database()
    {
        $source = JobPost::factory(10)->make();

        $persistFromSource = app(PersistFromSource::class);
        $persistFromSource->do($source->toArray());

        $this->assertCount(10, JobPost::all());
    }

    public function test_adds_us_location()
    {
        $source = JobPost::factory()->make([
            'location' => null,
            'body' => 'Must be authorized to work in the United states' 
        ]);

        $persistFromSource = app(PersistFromSource::class);
        $persistFromSource->do([$source->toArray()]);

        $addedJob = JobPost::first();
        $this->assertEquals('US', $addedJob->location);
    }
}
