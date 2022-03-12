<?php


namespace App;


use App\Billing\PaymentGateway;
use App\Models\JobPost;
use App\Models\Order;
use Illuminate\Mail\Markdown;

class PendingOrder
{
    public $options;
    public $position;

    public function __construct($options = [])
    {
        $this->options = $options;
        $this->position = $options['position'] ?? '';
    }

    public function ringUp(PaymentGateway $paymentGateway)
    {
        $checkoutSessionId = $paymentGateway->createCheckoutSession($this->total(), $this->position, $this->description());
        $jobPost = $this->createJobPost();
        $this->createOrderFor($jobPost, $checkoutSessionId);

        return $checkoutSessionId;
    }

    public function createOrderFor($job, $checkoutSession)
    {
        $job->order()->save(Order::make([
            'total' => $this->total(),
            'sticky' => $this->isSticky(),
            'discount' => 0,
            'paid' => false,
            'color' => $this->color(),
            'logo_path' => $this->logoPath(),
            'checkout_session' => $checkoutSession,
        ]));
    }

    public function total()
    {
        $total = config('prices.30_day_post');
        if ($this->isSticky()) {
            $total += config('prices.30_day_sticky');
        }

        if ($this->hasColorHighlight()) {
            $total += config('prices.company_color_highlight');
        }

        if ($this->hasLogo()) {
            $total += config('prices.company_logo');
        }

        return $total;
    }

    public function description()
    {
        $description = "30-day job post on Laravel Remote";
        if ($this->isSticky()) {
            $description .= ", sticky for 30 days";
        }

        if ($this->hasColorHighlight()) {
            $description .= ", highlight post with company color";
        }

        if ($this->hasLogo()) {
            $description .= ", with company logo";
        }

        return "$description.";
    }

    public function isSticky()
    {
        return $this->options['sticky'] ?? false;
    }

    public function hasColorHighlight()
    {
        return $this->options['with_company_color'] ?? false;
    }

    public function hasLogo()
    {
        return $this->options['with_logo'] ?? false;
    }

    public function color()
    {
        if ($this->hasColorHighlight()) {
            return $this->options['company_color'];
        }

        return null;
    }

    public function logoPath()
    {
        if ($this->hasLogo()) {
            $this->options['logo_path'] = $this->options['logo']->store('company_images', ['disk' => 'public']);
            return $this->options['logo_path'];
        }

        return null;
    }

    protected function createJobPost()
    {
        $job = JobPost::create([
            'company' => $this->options['company'],
            'position' => $this->options['position'],
            'job_type' => $this->options['job_type'],
            'location' => $this->options['location'],
            'salary_min' => $this->options['salary_min'],
            'salary_max' => $this->options['salary_max'],
            'salary_currency' => $this->options['salary_currency'],
            'salary_unit' => $this->options['salary_unit'],
            'body' => Markdown::parse($this->options['body']),
            'apply_url' => $this->options['apply_url'],
            'source_url' => $this->options['apply_url'],
            'source_name' => "laravel_remote",
            'source_created_at' => now(),
            'visible' => false,
        ]);

        $job->tags()->attach($this->options['tags']);

        return $job;
    }
}
