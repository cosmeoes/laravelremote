<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use PHPUnit\Framework\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    public function test_creates_checkout()
    {
        $paymentGateway = new FakePaymentGateway();

        // create checkout
        $sessionId = $paymentGateway->createCheckoutSession(30000, "Laravel Remote Developer", "Pinned, custom color, with logo");
        
        $this->assertCount(1, $paymentGateway->checkouts());
        $this->assertNotEmpty($sessionId);
        $checkout = $paymentGateway->checkout($sessionId);
        $this->assertNotNull($checkout);
        $this->assertEquals(30000, $checkout->total);
    }
}
