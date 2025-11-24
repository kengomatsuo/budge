<?php

use App\Services\CurrencyConverter;

if (! function_exists('convert_currency')) {
    /**
     * Convert an amount from one currency to another using CurrencyConverter.
     * Returns a float.
     */
    function convert_currency($amount, $from, $to)
    {
        try {
            return CurrencyConverter::convert((float) $amount, $from ?: 'IDR', $to ?: 'IDR');
        } catch (\Throwable $e) {
            return $amount;
        }
    }
}
