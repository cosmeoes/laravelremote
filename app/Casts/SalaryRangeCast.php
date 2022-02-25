<?php


namespace App\Casts;


use App\Models\SalaryRange;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class SalaryRangeCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return new SalaryRange(
            $attributes['salary_min'],
            $attributes['salary_max'],
            $attributes['salary_currency'],
            $attributes['salary_unit'],
        );
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return [
            'salary_min' => $value->min,
            'salary_max' => $value->max,
            'salary_currency' => $value->currency,
        ];
    }
}
