<?php

namespace App\Billing;

interface PaymentGateway
{
    public function createCheckoutSession($total, $productName, $productDescription, $email);
    public function checkouts();
    public function checkout($sessionId);
    public function event($payload, $signature);
    public function getValidTestSignature($payload);
    public function checkoutsDuring($callback);
    public function lineItems($checkoutSession);
}
