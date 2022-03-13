<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\InvalidEventException;
use App\Billing\StripePaymentGateway;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    public function test_creates_checkout()
    {
        $paymentGateway = new StripePaymentGateway("sk_test_51KckIwCNTnV9Sv5KUxfNu5JBWMGNvqyyEvKShv8E3StR4GrVPIvpRdTRQj98GNHgu6RiF2FiZzon3WYLo5WVj8n600bU6oLzbm", "");

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
        $paymentGateway = new StripePaymentGateway("sk_test_51KckIwCNTnV9Sv5KUxfNu5JBWMGNvqyyEvKShv8E3StR4GrVPIvpRdTRQj98GNHgu6RiF2FiZzon3WYLo5WVj8n600bU6oLzbm", "test-secret");

        $payload = file_get_contents(__DIR__ . '/../../Stubs/stripe-checkout-completed-event.json');
        $signature = $paymentGateway->getValidTestSignature($payload);
        $event = $paymentGateway->event($payload, $signature);
        $this->assertNotNull($event);
        $this->assertEquals('checkout.session.completed', $event->type);
    }

    public function test_throws_exception_if_is_invalid_signature()
    {
        $this->expectException(InvalidEventException::class);
        $paymentGateway = new StripePaymentGateway("sk_test_51KckIwCNTnV9Sv5KUxfNu5JBWMGNvqyyEvKShv8E3StR4GrVPIvpRdTRQj98GNHgu6RiF2FiZzon3WYLo5WVj8n600bU6oLzbm", "test-secret");
        $payload = json_decode(file_get_contents(__DIR__ . '/../../Stubs/stripe-checkout-completed-event.json'), true);
        $paymentGateway->event($payload, 'invalid-signature');
    }
}
