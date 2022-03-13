<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Http\Requests\PurshaseJobPostRequest;
use App\PendingOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
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

    public function show($checkoutSession)
    {
        $checkout = $this->paymentGateway->checkout($checkoutSession);
        if (!$checkout) {
            abort(404);
        }

        return view('orders.success', ['checkout' => $checkout]);
    }

}
