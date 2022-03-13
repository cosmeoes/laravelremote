<?php

namespace Tests\Unit;

use App\Mail\OrderCompletedEmail;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrderCompleteEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_contains_a_link_to_edit_job_post()
    {
        $order = Order::factory()->create();

        $email = new OrderCompletedEmail($order);

        $this->assertStringContainsString(URL::signedRoute('order.edit', ['id' => $order->id]), $email->render());
    }

    public function test_has_correct_subject()
    {
        $order = Order::factory()->create();

        $email = new OrderCompletedEmail($order);

        $this->assertStringContainsString("Your Laravel Remote Job Post is now live!", $email->build()->subject);
    }
}
