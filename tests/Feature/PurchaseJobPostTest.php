<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\JobPost;
use App\Models\Order;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurchaseJobPostTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    // features
    // - user can buy a new jobs post using credit card
    // - user recives an email with edit link
    // - user can edit created job posts using the link
    // - user can buy pined jobs (30 day), colors, company logo
    public function test_visitor_can_purchase_job_post()
    {
        $tagsIds = Tag::factory(3)->create()->pluck('id')->toArray();

        $response = $this->json('POST', route('job-post.create'), $this->validParameters([
            'tags' => $tagsIds,
            'sticky' => true,
            'with_company_color' => true,
            'company_color' => "#ff4742",
        ]));

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
        $this->assertCount(1, $this->paymentGateway->checkouts());
        $this->assertArrayHasKey('checkout_session', $json);
        $this->assertEquals($order->checkout_session, $json['checkout_session']);
        $checkout = $this->paymentGateway->checkout($json['checkout_session']);
        $this->assertNotNull($checkout);
        $this->assertEquals($order->total, $this->paymentGateway->checkout($json['checkout_session'])->total);
    }

    public function test_job_post_costs_configured_price()
    {
        $cost = config('prices.30_day_post');
        $response = $this->json('POST', route('job-post.create'), $this->validParameters());

        $response->assertStatus(201);
        $json = $response->json();
        $order = Order::query()->first();
        $checkout = $this->paymentGateway->checkout($json['checkout_session']);
        $this->assertEquals($checkout->id, $order->checkout_session);
        $this->assertEquals($cost, $this->paymentGateway->checkout($json['checkout_session'])->total);
        $this->assertEquals($cost, $order->total);
        $this->assertFalse($order->sticky);
        $this->assertNull($order->color);
    }

    public function test_can_buy_sticky_job_post()
    {
        $baseCost = config('prices.30_day_post');
        $stickyCost = config('prices.30_day_sticky');

        $response = $this->json('POST', route('job-post.create'), $this->validParameters([
            'sticky' => true,
        ]));

        $response->assertStatus(201);
        $json = $response->json();
        $order = Order::query()->first();
        $checkout = $this->paymentGateway->checkout($json['checkout_session']);
        $this->assertEquals($checkout->id, $order->checkout_session);
        $this->assertEquals($baseCost + $stickyCost, $checkout->total);
        $this->assertEquals($baseCost + $stickyCost, $order->total);
        $this->assertTrue($order->sticky);
        $this->assertStringContainsString("sticky for 30 days", $checkout->description);
    }

    public function test_can_buy_company_color()
    {
        $baseCost = config('prices.30_day_post');
        $colorCost = config('prices.company_color_highlight');

        $response = $this->json('POST', route('job-post.create'), $this->validParameters([
            'with_company_color' => true,
            'company_color' => "#FFFFFF",
        ]));

        $response->assertStatus(201);
        $json = $response->json();
        $order = Order::query()->first();
        $checkout = $this->paymentGateway->checkout($json['checkout_session']);
        $this->assertEquals($checkout->id, $order->checkout_session);
        $this->assertEquals($baseCost + $colorCost, $checkout->total);
        $this->assertEquals($baseCost + $colorCost, $order->total);
        $this->assertNotEquals("#FFFFF", $order->color);
        $this->assertStringContainsString("highlight post with company color", $checkout->description);
    }

    public function test_can_buy_job_post_with_logo()
    {
        $baseCost = config('prices.30_day_post');
        $logoCost = config('prices.company_logo');
        Storage::fake('public');

        $response = $this->json('POST', route('job-post.create'), $this->validParameters([
            'with_logo' => true,
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]));

        $response->assertStatus(201);
        $json = $response->json();
        $order = Order::query()->first();
        $checkout = $this->paymentGateway->checkout($json['checkout_session']);
        $this->assertEquals($checkout->id, $order->checkout_session);
        $this->assertEquals($baseCost + $logoCost, $checkout->total);
        $this->assertEquals($baseCost + $logoCost, $order->total);
        $this->assertStringContainsString("with company logo", $checkout->description);
        $this->assertNotNull($order->logo_path);
        Storage::disk('public')->assertExists($order->logo_path);
    }

    public function test_can_buy_job_post_with_all_options()
    {
        $baseCost = config('prices.30_day_post');
        $logoCost = config('prices.company_logo');
        $colorCost = config('prices.company_color_highlight');
        $stickyCost = config('prices.30_day_sticky');
        Storage::fake('public');

        $response = $this->json('POST', route('job-post.create'), $this->validParameters([
            'sticky' => true,
            'with_company_color' => true,
            'company_color' => "#FFFFFF",
            'with_logo' => true,
            'logo' => UploadedFile::fake()->image('logo.png'),
        ]));

        $response->assertStatus(201);
        $json = $response->json();
        $order = Order::query()->first();
        $checkout = $this->paymentGateway->checkout($json['checkout_session']);
        $this->assertEquals($checkout->id, $order->checkout_session);
        $this->assertEquals($baseCost + $logoCost + $colorCost + $stickyCost, $checkout->total);
        $this->assertEquals($baseCost + $logoCost + $colorCost + $stickyCost, $order->total);
        $this->assertStringContainsString("sticky for 30 days", $checkout->description);
        $this->assertStringContainsString("highlight post with company color", $checkout->description);
        $this->assertStringContainsString("with company logo", $checkout->description);
        $this->assertNotNull($order->logo_path);
        Storage::disk('public')->assertExists($order->logo_path);
    }

    public function validParameters($overwrite = [])
    {
        return array_merge([
            'company' =>  'Test company',
            'position' =>  'Laravel TDD Developer',
            'job_type' =>  'Full time',
            'tags' => [],
            'location' => 'Worldwide',
            'salary_min' => 60000,
            'salary_max' => 120000,
            'salary_currency' => 'USD',
            'salary_unit' => 'year',
            'body' => "# This is a remote laravel position",
            'apply_url' => 'https://laravelremote.com',
        ], $overwrite);
    }
}
