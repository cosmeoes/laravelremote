<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\InvalidEventException;
use PHPUnit\Framework\TestCase;

class FakePaymentGatewayTest extends TestCase
{
    public function test_creates_checkout()
    {
        $paymentGateway = new FakePaymentGateway();

        $checkoutsDuring = $paymentGateway->checkoutsDuring(function ($paymentGateway) use (&$sessionId) {
            $sessionId = $paymentGateway->createCheckoutSession(30000, "Laravel Remote Developer", "Pinned, custom color, with logo", "john.doe@company.com");
        });

        $this->assertCount(1, $checkoutsDuring);
        $this->assertNotEmpty($sessionId);
        $checkout = $paymentGateway->checkout($sessionId);
        $this->assertNotNull($checkout);
        $this->assertEquals(30000, $checkout->amount_total);
        $this->assertEquals("john.doe@company.com", $checkout->customer_email);
        $this->assertEquals("Laravel Remote Developer", $paymentGateway->lineItems($checkout->id)['data']['0']['description']);
    }

    public function test_can_create_event()
    {
        $paymentGateway = new FakePaymentGateway();

        $payload = file_get_contents(__DIR__ . '/../../Stubs/stripe-checkout-completed-event.json');
        $signature = $paymentGateway->getValidTestSignature($payload);
        $event = $paymentGateway->event($payload, $signature);
        $this->assertNotNull($event);
        $this->assertEquals('checkout.session.completed', $event->type);
    }

    public function test_throws_exception_if_is_invalid_signature()
    {
        $this->expectException(InvalidEventException::class);
        $paymentGateway = new FakePaymentGateway();
        $payload = json_decode(file_get_contents(__DIR__ . '/../../Stubs/stripe-checkout-completed-event.json'), true);
        $paymentGateway->event($payload, 'invalid-signature');
    }
}
