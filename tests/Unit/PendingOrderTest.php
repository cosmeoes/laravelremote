<?php

namespace Tests\Unit;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Models\JobPost;
use App\Models\Order;
use App\Models\Tag;
use App\PendingOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendingOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_total_and_description_for_base_post()
    {
        $pendingOrder = new PendingOrder();
        $this->assertEquals(config('prices.30_day_post'), $pendingOrder->total());
        $this->assertEquals("30-day job post on Laravel Remote.", $pendingOrder->description());
    }

    public function test_creates_total_and_description_for_sticky_post()
    {
        $pendingOrder = new PendingOrder([
            'sticky' => true,
        ]);

        $this->assertEquals(config('prices.30_day_post') + config('prices.30_day_sticky'), $pendingOrder->total());
        $this->assertEquals("30-day job post on Laravel Remote, sticky for 30 days.", $pendingOrder->description());
    }

    public function test_creates_total_and_description_for_color_post()
    {
        $pendingOrder = new PendingOrder([
            'with_company_color' => true,
            'color' => '#FFFFFF',
        ]);

        $this->assertEquals(config('prices.30_day_post') + config('prices.company_color_highlight'), $pendingOrder->total());
        $this->assertEquals("30-day job post on Laravel Remote, highlight post with company color.", $pendingOrder->description());
    }

    public function test_creates_total_and_description_for_post_with_logo()
    {
        $pendingOrder = new PendingOrder([
            'with_logo' => true,
            'logo' => 'some uploaded file',
        ]);

        $this->assertEquals(config('prices.30_day_post') + config('prices.company_logo'), $pendingOrder->total());
        $this->assertEquals("30-day job post on Laravel Remote, with company logo.", $pendingOrder->description());
    }

    public function test_calculates_total_and_description_for_mixed_orders()
    {
        $orderStickyAndWithColor = new PendingOrder([
            'sticky' => true,
            'with_company_color' => true,
            'color' => '#FFFFFF',
        ]);

        $orderColorAndLogo = new PendingOrder([
            'with_company_color' => true,
            'color' => '#FFFFFF',
            'with_logo' => true,
            'logo' => 'some uploaded file',
        ]);

        $orderStickyAndLogo = new PendingOrder([
            'sticky' => true,
            'with_logo' => true,
            'logo' => 'some uploaded file',
        ]);

        $fullOrder = new PendingOrder([
            'sticky' => true,
            'with_company_color' => true,
            'color' => '#FFFFFF',
            'with_logo' => true,
            'logo' => 'some uploaded file',
        ]);

        $this->assertEquals(config('prices.30_day_post') + config('prices.company_logo') + config('prices.30_day_sticky'), $orderStickyAndWithColor->total());
        $this->assertEquals("30-day job post on Laravel Remote, sticky for 30 days, highlight post with company color.", $orderStickyAndWithColor->description());

        $this->assertEquals(config('prices.30_day_post') + config('prices.company_color_highlight') + config('prices.company_logo'), $orderColorAndLogo->total());
        $this->assertEquals("30-day job post on Laravel Remote, highlight post with company color, with company logo.", $orderColorAndLogo->description());

        $this->assertEquals(config('prices.30_day_post') + config('prices.30_day_sticky') + config('prices.company_logo'), $orderStickyAndLogo->total());
        $this->assertEquals("30-day job post on Laravel Remote, sticky for 30 days, with company logo.", $orderStickyAndLogo->description());

        $this->assertEquals(config('prices.30_day_post') + config('prices.30_day_sticky') + config('prices.company_logo') + config('prices.company_color_highlight'), $fullOrder->total());
        $this->assertEquals("30-day job post on Laravel Remote, sticky for 30 days, highlight post with company color, with company logo.", $fullOrder->description());
    }

    public function test_can_get_order_name()
    {
        $pendingOrder = new PendingOrder([
            'position' => "This is a test position"
        ]);

        $this->assertEquals('This is a test position', $pendingOrder->position);
    }

    public function test_can_create_a_base_order()
    {
        $pendingOrder = new PendingOrder();
        $job = JobPost::factory()->create();

        $pendingOrder->createOrderFor($job, 'test_checkout_session');

        $this->assertEquals(1, Order::count());
        tap(Order::first(), function ($order) use ($pendingOrder, $job) {
            $this->assertEquals($pendingOrder->total(), $order->total);
            $this->assertTrue($job->order->is($order));
            $this->assertEquals('test_checkout_session', $order->checkout_session);
            $this->assertFalse($order->paid);
            $this->assertFalse($order->isSticky());
            $this->assertFalse($order->hasColorHighlight());
            $this->assertFalse($order->hasCompanyLogo());
        });
    }

    public function test_can_create_a_sticky_order()
    {
        $pendingOrder = new PendingOrder([
            'sticky' => true
        ]);

        $job = JobPost::factory()->create();

        $pendingOrder->createOrderFor($job, 'test_checkout_session');

        $this->assertEquals(1, Order::count());
        tap(Order::first(), function ($order) use ($pendingOrder, $job) {
            $this->assertEquals($pendingOrder->total(), $order->total);
            $this->assertTrue($job->order->is($order));
            $this->assertEquals('test_checkout_session', $order->checkout_session);
            $this->assertFalse($order->paid);
            $this->assertTrue($order->isSticky());
            $this->assertFalse($order->hasColorHighlight());
            $this->assertFalse($order->hasCompanyLogo());
        });
    }

    public function test_can_create_a_color_order()
    {
        $pendingOrder = new PendingOrder([
            'with_company_color' => true,
            'company_color' => '#FFFFFF',
        ]);

        $job = JobPost::factory()->create();

        $pendingOrder->createOrderFor($job, 'test_checkout_session');

        $this->assertEquals(1, Order::count());
        tap(Order::first(), function ($order) use ($pendingOrder, $job) {
            $this->assertEquals($pendingOrder->total(), $order->total);
            $this->assertTrue($job->order->is($order));
            $this->assertEquals('test_checkout_session', $order->checkout_session);
            $this->assertFalse($order->paid);
            $this->assertTrue($order->hasColorHighlight());
            $this->assertEquals("#FFFFFF", $order->color);
            $this->assertFalse($order->isSticky());
            $this->assertFalse($order->hasCompanyLogo());
        });
    }

    public function test_can_create_a_logo_order()
    {
        Storage::fake('public');
        $pendingOrder = new PendingOrder([
            'with_logo' => true,
            'logo' => UploadedFile::fake()->image('company_logo.png')
        ]);

        $job = JobPost::factory()->create();

        $pendingOrder->createOrderFor($job, 'test_checkout_session');

        $this->assertEquals(1, Order::count());
        tap(Order::first(), function ($order) use ($pendingOrder, $job) {
            $this->assertEquals($pendingOrder->total(), $order->total);
            $this->assertTrue($job->order->is($order));
            $this->assertEquals('test_checkout_session', $order->checkout_session);
            $this->assertFalse($order->paid);
            $this->assertTrue($order->hasCompanyLogo());
            Storage::disk('public')->assertExists($order->logo_path);
            $this->assertFalse($order->hasColorHighlight());
            $this->assertFalse($order->isSticky());
        });
    }

    public function test_can_create_full_order()
    {
        Storage::fake('public');
        $pendingOrder = new PendingOrder([
            'sticky' => true,
            'with_company_color' => true,
            'company_color' => '#FFFFFF',
            'with_logo' => true,
            'logo' => UploadedFile::fake()->image('company_logo.png')
        ]);

        $job = JobPost::factory()->create();

        $pendingOrder->createOrderFor($job, 'test_checkout_session');

        $this->assertEquals(1, Order::count());
        tap(Order::first(), function ($order) use ($pendingOrder, $job) {
            $this->assertEquals($pendingOrder->total(), $order->total);
            $this->assertTrue($job->order->is($order));
            $this->assertEquals('test_checkout_session', $order->checkout_session);
            $this->assertFalse($order->paid);
            $this->assertTrue($order->hasCompanyLogo());
            Storage::disk('public')->assertExists($order->logo_path);
            $this->assertTrue($order->hasColorHighlight());
            $this->assertEquals("#FFFFFF", $order->color);
            $this->assertTrue($order->isSticky());
        });
    }

    public function test_ring_up_pending_order()
    {
        $paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $paymentGateway);
        $tagsIds = Tag::factory(3)->create()->pluck('id')->toArray();
        $pendingOrder = new PendingOrder([
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
        ]);

        $checkoutSession = $pendingOrder->ringUp($paymentGateway);

        $this->assertEquals(1, JobPost::count());
        $jobPost = JobPost::first();
        $this->assertNotNull($jobPost->order);
        $this->assertNotNull($pendingOrder->total(), $jobPost->order->total);
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

        $checkout = $paymentGateway->checkout($checkoutSession);
        $this->assertNotNull($checkout);
        $this->assertEquals($pendingOrder->total(), $checkout->total);
        $this->assertEquals($pendingOrder->description(), $checkout->description);
    }
}
