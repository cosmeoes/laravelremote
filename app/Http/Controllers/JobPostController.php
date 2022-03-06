<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Models\JobPost;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;

class JobPostController extends Controller
{
    protected $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(Request $request)
    {
        $validated = $request->all();

        $total = 15900;
        $color = null;
        $description = "30-day job post on Laravel Remote.";
        if ($validated['pinned']) {
            $total += 4900;
            $description = " + stiky for 30 days";
        }

        if ($validated['with_company_color']) {
            $total += 15900;
            $color = $validated['company_color'];
            $description = " + highlight post with company color";
        }


        $checkoutSessionId = $this->paymentGateway->createCheckoutSession($total, $validated['position'], $description);

        $job = JobPost::create([
            'company' => $validated['company_name'],
            'position' => $validated['position'],
            'job_type' => $validated['job_type'],
            'location' => $validated['location'],
            'salary_min' => $validated['salary_min'],
            'salary_max' => $validated['salary_max'],
            'salary_currency' => $validated['salary_currency'],
            'salary_unit' => $validated['salary_unit'],
            'body' => Markdown::parse($validated['body']),
            'apply_url' => $validated['apply_url'],
            'source_url' => $validated['apply_url'],
            'source_name' => "laravel_remote",
            'source_created_at' => now(),
            'visible' => false,
        ]);

        $job->tags()->attach($validated['tags']);

        $job->order()->save(Order::make([
            'total' => $total,
            'color' => $color,
            'pinned' => $validated['pinned'],
            'discount' => 0,
            'paid' => false,
            'checkout_session' => $checkoutSessionId,
        ]));

        return response()->json([
            'checkout_session' => $checkoutSessionId
        ], 201);
    }
}
