<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\JobPost;
use App\Models\Tag;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchaseJobPostTest extends TestCase
{
    use DatabaseMigrations;
    // features 
    // - user can buy a new jobs post using credit card
    // - user recives an email with edit link
    // - user can edit created job posts using the link
    // - user can buy pined jobs (30 day), colors, company logo
    public function test_visitor_can_purchase_job_post()
    {
        $paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $tagsIds = Tag::factory(3)->create()->pluck('id')->toArray();
        $response = $this->json('POST', route('job-post.create'), [
            'job_post' => [
                'company' =>  'Test company',
                'position' =>  'Laravel TDD Developer',
                'job_type' =>  'Full time',
                'tags' => $tagsIds,
                'location' => 'Worldwide',
                'salary_min' => 60000,
                'salary_max' => 120000,
                'salary_currency' => 'USD',
                'salary_unit' => 'year',
                'body' => "# This is a remote laravel position",
                'apply_url' => 'https://laravelremote.com',
            ],
            'show_logo' => false,
            'sticky' => true,
            'with_company_color' => true,
            'company_color' => "#ff4742",
        ]);


        $response->assertStatus(201);

        $jobPost = JobPost::where('company', 'Test company')->first();
        $this->assertNotNull($jobPost);
        $this->assertEquals('Laravel TDD Developer', $jobPost->position);
        $this->assertEquals('Full time', $jobPost->job_type);
        $this->assertEquals($tagsIds, $jobPost->tags->pluck('id')->toArray());
        $this->assertEquals('Worldwide', $jobPost->location);
        $this->assertEquals(60000, $jobPost->salary_min);
        $this->assertEquals(120000, $jobPost->salary_max);
        $this->assertEquals('USD', $jobPost->salary_currency);
        $this->assertEquals('year', $jobPost->salary_unit);
        $this->assertEquals("laravel_remote", $jobPost->source_name);
        $this->assertEquals('https://laravelremote.com', $jobPost->apply_url);
        $this->assertEquals("<h1>This is a remote laravel position</h1>\n", $jobPost->body);
        $this->assertFalse($jobPost->visible);

        $order = $jobPost->order;
        $this->assertNotNull($order);
        // 159 post, 49 color, 159 pinned
        $this->assertNotNull((159 + 49 + 159) * 100, $order->total);
        $this->assertNotNull("#ff4742", $order->color);
        $this->assertNull($order->logo);
        $this->assertTrue($order->sticky);
        $this->assertEquals(0, $order->discount);
        $this->assertNull($order->cupon);
        $this->assertFalse($order->paid);

        $json = $response->json();
        $this->assertCount(1, $paymentGateway->checkouts());
        $this->assertArrayHasKey('checkout_session', $json);
        $this->assertEquals($order->checkout_session, $json['checkout_session']);
        $checkout = $paymentGateway->checkout($json['checkout_session']);
        $this->assertNotNull($checkout);
        $this->assertEquals($order->total, $paymentGateway->checkout($json['checkout_session'])->total);
    }
}
