<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\PendingOrder;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(Request $request)
    {
        $pendingOrder = new PendingOrder($request->all());

        return response()->json([
            'checkout_session' => $pendingOrder->ringUp($this->paymentGateway)
        ], 201);
    }
}
