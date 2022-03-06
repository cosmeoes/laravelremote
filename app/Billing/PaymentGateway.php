<?php

namespace App\Billing;

interface PaymentGateway
{
    public function createCheckoutSession($total, $productName, $productDescription);
}
