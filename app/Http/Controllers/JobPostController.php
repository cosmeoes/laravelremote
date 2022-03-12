<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Http\Requests\PurshaseJobPostRequest;
use App\PendingOrder;

class JobPostController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(PurshaseJobPostRequest $request)
    {
        $pendingOrder = new PendingOrder($request->all());

        return response()->json([
            'checkout_session' => $pendingOrder->ringUp($this->paymentGateway)
        ], 201);
    }
}
