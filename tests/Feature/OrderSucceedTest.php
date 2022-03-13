<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderSucceedTest extends TestCase
{
    protected $paymentGateway;

    public function setUp(): void
    {
        parent::setUp();
        $this->paymentGateway = new FakePaymentGateway();
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);
    }

    public function test_can_view_success_page()
    {
        $checkoutSession = $this->paymentGateway->createCheckoutSession(1000, 'Test product', 'Product descriptions', 'joe.doe@company.com');
        $checkout = $this->paymentGateway->checkout($checkoutSession);
        $response = $this->get(route('checkout.success', ['checkout_session' => $checkoutSession]));

        $response->assertStatus(200);
        $response->assertViewIs('orders.success');
        $response->assertViewHas('checkout', function($viewCheckout) use ($checkout) {
            return $viewCheckout == $checkout;
        });
    }

    public function test_cant_view_non_existent_success_page()
    {
        $response = $this->get(route('checkout.success', ['checkout_session' => 'fakecheckout']));
        $response->assertStatus(404);
    }
}
