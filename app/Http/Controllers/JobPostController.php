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

        $total = config('prices.30_day_post');
        $color = null;
        $description = "30-day job post on Laravel Remote.";
        if ($validated['sticky']) {
            $total += config('prices.30_day_sticky');
            $description = " + stiky for 30 days";
        }

        if ($validated['with_company_color']) {
            $total += config('prices.company_color_highlight');
            $color = $validated['company_color'];
            $description = " + highlight post with company color";
        }


        $jobData = $validated['job_post'];
        $checkoutSessionId = $this->paymentGateway->createCheckoutSession($total, $jobData['position'], $description);

        $job = JobPost::create([
            'company' => $jobData['company'],
            'position' => $jobData['position'],
            'job_type' => $jobData['job_type'],
            'location' => $jobData['location'],
            'salary_min' => $jobData['salary_min'],
            'salary_max' => $jobData['salary_max'],
            'salary_currency' => $jobData['salary_currency'],
            'salary_unit' => $jobData['salary_unit'],
            'body' => Markdown::parse($jobData['body']),
            'apply_url' => $jobData['apply_url'],
            'source_url' => $jobData['apply_url'],
            'source_name' => "laravel_remote",
            'source_created_at' => now(),
            'visible' => false,
        ]);

        $job->tags()->attach($jobData['tags']);

        $job->order()->save(Order::make([
            'total' => $total,
            'color' => $color,
            'sticky' => $validated['sticky'],
            'discount' => 0,
            'paid' => false,
            'checkout_session' => $checkoutSessionId,
        ]));

        return response()->json([
            'checkout_session' => $checkoutSessionId
        ], 201);
    }
}
