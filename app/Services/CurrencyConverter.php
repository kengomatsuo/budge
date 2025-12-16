<?php

namespace App\Services;

class CurrencyConverter
{
    /**
     * Convert amount from one currency to another using static rates.
     * Falls back to 1:1 if rate missing.
     */
    public static function convert(float $amount, string $from, string $to): float
    {
        $from = strtoupper($from ?: 'IDR');
        $to = strtoupper($to ?: 'IDR');

        if ($from === $to) {
            return $amount;
        }

        // Try live conversion via ExchangeRateService
        // If it fails, it will throw an exception which we allow to bubble up
        $converted = ExchangeRateService::convert($amount, $from, $to);

        if (!is_null($converted)) {
            return $converted;
        }

        throw new \Exception("CurrencyConverter: Unable to convert from $from to $to");
    }
}
