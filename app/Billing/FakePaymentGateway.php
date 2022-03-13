<?php

namespace App\Billing;

use Illuminate\Support\Str;

class FakePaymentGateway implements PaymentGateway
{
    private $checkouts;

    public function __construct()
    {
        $this->checkouts = collect();
    }

    public function createCheckoutSession($total, $productName, $productDescription, $email)
    {
        $sessionId = Str::uuid()->toString();
        $this->checkouts->add((object) [
            'id' => $sessionId,
            'amount_total' => $total,
            'line_items' => [
                'data' => [
                    [
                        'description' => $productName,
                    ]
                ]
            ],
            'customer_email' => $email
        ]);

        return $sessionId;
    }


    public function checkouts()
    {
        return $this->checkouts;
    }

    public function checkout($sessionId)
    {
        return $this->checkouts
                ->first(fn ($checkout) => $checkout->id == $sessionId);

    }

    public function getValidTestSignature($payload)
    {
        return 'valid-signature';
    }

    public function event($payload, $signature)
    {
        if ($signature !== $this->getValidTestSignature($payload)) {
            throw new InvalidEventException();
        }
        return json_decode($payload);
    }

    public function checkoutsDuring($callback)
    {
        $checkoutCount = $this->checkouts->count();

        $callback($this);

        return $this->checkouts->skip($checkoutCount);
    }

    public function lineItems($checkoutSession)
    {
        return $this->checkout($checkoutSession)->line_items;
    }
}
