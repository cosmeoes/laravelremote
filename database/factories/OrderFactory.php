<?php

namespace Database\Factories;

use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'job_post_id' => fn() => JobPost::factory(),
            'total' => $this->faker->numberBetween(10000, 1000000),
            'sticky' => false,
            'discount' => 0,
            'paid' => false,
            'email' => $this->faker->email(),
            'checkout_session' => $this->faker->text(10),
        ];
    }
}
