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

    public function createCheckoutSession($total, $productName, $productDescription)
    {
        $sessionId = Str::uuid();
        $this->checkouts->add((object) [
            'id' => $sessionId,
            'total' => $total,
            'description' => $productDescription
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
}
