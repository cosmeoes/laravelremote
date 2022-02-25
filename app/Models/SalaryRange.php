<?php

namespace App\Models;

use Illuminate\Support\Str;
use NumberFormatter;
use Symfony\Component\Intl\Currencies;

class SalaryRange {
    public function __construct(
        public $min,
        public $max,
        public $currency,
        public $unit,
    ) {}

    public function __toString(): string
    {
        if ($this->isEmpty()) {
            return '';
        }

        [$min, $max] = [$this->min, $this->max];

        if ($min > $max) {
            [$max, $min] = [$min, $max];
        }

        if ($min <= 0) {
            return "From " . $this->formatMoney($max) . ' / ' . $this->unit();
        }

        return  $this->formatMoney($min) . " - " . $this->formatMoney($max) . ' / ' . $this->unit();
    }

    public function formatMoney($amount)
    {
        return $this->currencySymbol() . (new NumberFormatter('en_US', NumberFormatter::PADDING_POSITION))->format($amount);
    }

    public function isEmpty()
    {
        return $this->min <= 0 && $this->max <= 0;
    }

    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    public function unit()
    {
        $default = 'year';
        if (!$this->unit) {
            return $default;
        }

        $searches = [
            'year' => 'year',
            'hour' => 'hour',
            'month' => 'month',
            'week' => 'week',
        ];

        foreach ($searches as $needle => $value) {
            if (Str::contains($this->unit, $needle, true)) {
                return $value;
            }
        }

        return $default;
    }

    public function currencySymbol()
    {
        if (!$this->currency) {
            return "$";
        }

        return Currencies::getSymbol($this->currency);
    }
}
