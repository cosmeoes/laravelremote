<?php

namespace Tests\Feature;

use App\Console\Commands\SendJobAlert;
use App\Models\JobPost;
use App\Services\Mailchimp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendJobAlertsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_daily_alerts()
    {
        $jobDaily = JobPost::factory()->createOne(['created_at' => now()->subHours(10)]);
        $jobNotDaily = JobPost::factory()->createOne(['created_at' => now()->subHours(25)]);

        $testDouble = $this->getMailchimpDouble();
        $this->artisan(SendJobAlert::class, ['--type' => 'daily']);
        $this->assertEquals("Today's Laravel Remote Jobs", $testDouble->subject);
        $this->assertEquals(intval(config('services.mailchimp.daily_segment_id')), $testDouble->segmentId);
        $this->assertStringContainsString($jobDaily->source_url, $testDouble->html);
        $this->assertStringContainsString($jobDaily->position, $testDouble->html);
        $this->assertStringNotContainsString($jobNotDaily->source_url, $testDouble->html);
        $this->assertStringNotContainsString($jobNotDaily->position, $testDouble->html);
    }

    public function test_sends_weekly_alerts()
    {
        $jobWeekly = JobPost::factory()->createOne(['created_at' => now()->subDays(5)]);
        $jobNotWeekly = JobPost::factory()->createOne(['created_at' => now()->subDays(8)]);

        $testDouble = $this->getMailchimpDouble();
        $this->artisan(SendJobAlert::class, ['--type' => 'weekly']);
        $this->assertEquals("This week's Laravel Remote Jobs", $testDouble->subject);
        $this->assertEquals(intval(config('services.mailchimp.weekly_segment_id')), $testDouble->segmentId);
        $this->assertStringContainsString($jobWeekly->source_url, $testDouble->html);
        $this->assertStringContainsString($jobWeekly->position, $testDouble->html);
        $this->assertStringNotContainsString($jobNotWeekly->source_url, $testDouble->html);
        $this->assertStringNotContainsString($jobNotWeekly->position, $testDouble->html);
    }

    public function getMailchimpDouble()
    {
        $testDouble = new class extends Mailchimp {
            public $html;
            public $subject;
            public $segmentId;

            public function __construct() {}

            public function sendEmail($html, $subject, $segmentId)
            {
                $this->html = $html;
                $this->subject = $subject;
                $this->segmentId = $segmentId;
            }
        };

        $this->swap(
            Mailchimp::class,
            $testDouble
        );

        return $testDouble;
    }
}
