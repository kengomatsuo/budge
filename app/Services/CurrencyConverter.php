<?php

namespace App\Services;

class CurrencyConverter
{
    /**
     * Static conversion rates relative to IDR. Update as needed or replace with API.
     * Key: currency code, Value: number of IDR per unit of that currency.
     */
    protected static $ratesToIdr = [
        'IDR' => 1.0,
        'USD' => 17000.0,
        'EUR' => 20500.0,
        'JPY' => 108.0,
    ];

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

        // Try live conversion via ExchangeRateService if available
        try {
            $converted = ExchangeRateService::convert($amount, $from, $to);
            if (!is_null($converted)) {
                return $converted;
            }
        } catch (\Throwable $e) {
            // fall back to static rates
        }

        $rates = self::$ratesToIdr;

        $fromToIdr = $rates[$from] ?? null;
        $toToIdr = $rates[$to] ?? null;

        // If either rate is missing, return original amount as fallback
        if (is_null($fromToIdr) || is_null($toToIdr)) {
            return $amount;
        }

        // Convert amount -> IDR -> target
        $amountInIdr = $amount * $fromToIdr;
        $converted = $amountInIdr / $toToIdr;

        return $converted;
    }
}
