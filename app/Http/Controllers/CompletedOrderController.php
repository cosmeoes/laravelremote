<?php

namespace App\Http\Controllers;

use App\Billing\InvalidEventException;
use App\Billing\PaymentGateway;
use App\Mail\OrderCompletedEmail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CompletedOrderController extends Controller
{
    public function __construct(protected PaymentGateway $paymentGateway) {}

    public function store()
    {
        try {
            $event = $this->paymentGateway->event(request()->getContent(), request()->header('Stripe-Signature'));

            if ($event->type !== 'checkout.session.completed') {
                return response()->noContent(200);
            }

            $order = Order::query()->where('checkout_session', request('data.object.id'))->firstOrFail();
            abort_if($order->paid, 422);
            $order->update(['paid' => true]);
            $order->jobPost()->update([
                'visible' => true
            ]);

            Mail::send(new OrderCompletedEmail($order));
        } catch (InvalidEventException) {
            abort(400);
        }

        return response()->noContent(200);
    }
}
