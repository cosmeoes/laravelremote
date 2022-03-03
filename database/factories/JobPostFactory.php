<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPost>
 */
class JobPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'position' => $this->faker->text(20),
            'location' => $this->faker->timezone(),
            'job_type' => $this->faker->text(8),
            'company' => $this->faker->company(),
            'body' => $this->faker->randomHtml(),
            'salary_max' => $this->faker->numberBetween(60000, 100000),
            'salary_min' => $this->faker->numberBetween(40000, 60000),
            'salary_currency' => $this->faker->currencyCode(),
            'salary_unit' => $this->faker->text(5),
            'source_name' => $this->faker->text(5),
            'source_url' => $this->faker->url(),
            'apply_url' => $this->faker->url(),
            'source_created_at' => now(),
        ];
    }

    public function taggable()
    {
        return $this->state(function (array $attributes) {

            $tags = config('tags');
            shuffle($tags);
            $tagText = reset($tags)['matches'][0];
            return [
                'body' => 'contains tag matches ' . $tagText 
            ];
        });
    }
}
