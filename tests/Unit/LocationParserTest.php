<?php

namespace Tests\Feature;

use App\Helpers\LocationParser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LocationParserTest extends TestCase
{
    public function test_parsers_authorized_us()
    {
        $parser = new LocationParser();
        $location = $parser->parse("<b>This is a example job post. Must be authorized to work in the U.S.</b>");
        $locationB = $parser->parse("<b>This is a example job post. Must be authorized to work in the United States</b>");
        $locationC = $parser->parse("<b>This is a example job post. Must be authorized to work in the <b>United States</b></b>");

        $this->assertEquals('US', $location);
        $this->assertEquals('US', $locationB);
        $this->assertEquals('US', $locationC);
    }

    public function test_it_returns_null_for_not_found_locations()
    {
        $parser = new LocationParser();
        $location = $parser->parse("This contains no location dog");

        $this->assertNull($location);
    }
}
