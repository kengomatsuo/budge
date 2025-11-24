<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    // cache key prefix
    protected static $cacheKey = 'exchange_rates_';

    // TTL in seconds (12 hours)
    protected static $ttl = 43200;

    /**
     * Get latest rates for a base currency using configured provider (cached).
     * For ExchangeRate-API v6 we call: https://v6.exchangerate-api.com/v6/{key}/latest/{base}
     * Returns associative array of rates or null on failure.
     */
    public static function getRates(string $base = 'IDR') : ?array
    {
        $base = strtoupper($base ?: 'IDR');
        $key = self::$cacheKey . $base;

        return Cache::remember($key, self::$ttl, function () use ($base) {
            try {
                $provider = config('services.exchange_rate.provider', 'exchangerate_api_v6');

                if ($provider === 'exchangerate_api_v6') {
                    $apiKey = config('services.exchange_rate.key');
                    $endpointTemplate = config('services.exchange_rate.v6_endpoint', 'https://v6.exchangerate-api.com/v6/%s/latest/%s');

                    if (empty($apiKey)) {
                        return null;
                    }

                    $url = sprintf($endpointTemplate, $apiKey, $base);
                    $resp = Http::timeout(6)->get($url);

                    if ($resp->ok()) {
                        $json = $resp->json();
                        // ExchangeRate-API v6 returns 'conversion_rates'
                        if (isset($json['conversion_rates']) && is_array($json['conversion_rates'])) {
                            return $json['conversion_rates'];
                        }
                        // older/alternate structure
                        if (isset($json['rates']) && is_array($json['rates'])) {
                            return $json['rates'];
                        }
                    }
                }

                // Fallback to exchangerate.host if configured or v6 fails
                $resp = Http::timeout(5)->get('https://api.exchangerate.host/latest', [
                    'base' => $base,
                ]);

                if ($resp->ok()) {
                    $json = $resp->json();
                    return $json['rates'] ?? null;
                }
            } catch (\Throwable $e) {
                return null;
            }
            return null;
        });
    }

    /**
     * Convert amount from one currency to another using live rates when available.
     * Returns null on failure to indicate no conversion available.
     */
    public static function convert(float $amount, string $from, string $to) : ?float
    {
        $from = strtoupper($from ?: 'IDR');
        $to = strtoupper($to ?: 'IDR');

        if ($from === $to) {
            return $amount;
        }

        // Prefer retrieving rates with base = from
        $rates = self::getRates($from);
        if (is_array($rates) && isset($rates[$to])) {
            $rate = $rates[$to];
            return $amount * $rate;
        }

        // Attempt fallback: get rates for base = IDR and compute via IDR
        $ratesFromIdr = self::getRates('IDR');
        if (is_array($ratesFromIdr) && isset($ratesFromIdr[$from]) && isset($ratesFromIdr[$to])) {
            // ratesFromIdr maps IDR -> X, so to convert from->to: amount * (IDR_per_from / IDR_per_to)
            $idrPerFrom = $ratesFromIdr[$from];
            $idrPerTo = $ratesFromIdr[$to];
            if ($idrPerTo == 0) return null;
            return $amount * ($idrPerFrom / $idrPerTo);
        }

        return null;
    }
}
