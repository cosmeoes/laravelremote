<?php

namespace App\Billing;

use Illuminate\Support\Arr;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripePaymentGateway implements PaymentGateway
{
    protected $client;
    protected $endpointSecret;

    public function __construct($apiKey, $endpointSecret)
    {
        $this->client = new StripeClient($apiKey);
        $this->endpointSecret = $endpointSecret;
    }

    public function createCheckoutSession($total, $productName, $productDescription, $email)
    {
        $checkoutSession = $this->client->checkout->sessions->create([
            'success_url' => route('checkout.success', ['checkout_session' => rawurlencode('{CHECKOUT_SESSION_ID}')]),
            'cancel_url' => 'https://example.com/cancel',
            'customer_email' => $email,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => "USD",
                        'product_data' => [
                            'name' => $productName,
                            'description' => $productDescription
                        ],
                        'unit_amount' => $total,
                    ],
                    'quantity' => 1
                ],
            ],
            'mode' => 'payment',
        ]);

        return $checkoutSession->id;
    }

    public function checkout($sessionId)
    {
        return $this->client->checkout->sessions->retrieve($sessionId);
    }

    public function event($payload, $signature)
    {
        try {
            return Webhook::constructEvent(
                $payload, $signature, $this->endpointSecret
            );
        } catch (SignatureVerificationException) {
            throw new InvalidEventException();
        }
    }

    public function checkouts()
    {
        return $this->client->checkout->sessions->all();
    }

    public function checkoutsDuring($callback)
    {
        $lastCheckoutId = Arr::get($this->client->checkout->sessions->all(['limit' => 1]), 'data.0.id');

        $callback($this);

        $data = [];

        if ($lastCheckoutId) {
            $data = ['ending_before' => $lastCheckoutId];
        }

        return $this->client->checkout->sessions->all($data);
    }

    public function getValidTestSignature($payload)
    {
        $timestamp = now()->timestamp;
        $signedPayload = "{$timestamp}.{$payload}";
        $signature = hash_hmac('sha256', $signedPayload, $this->endpointSecret);
        return "t=$timestamp,v1=$signature,v0=6ffbb59b2300aae63f272406069a9788598b792a944a07aba816edb039989a39";
    }

    public function lineItems($checkoutId)
    {
        return $this->client->checkout->sessions->allLineItems($checkoutId);
    }
}
