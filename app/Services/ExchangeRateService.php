<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            $provider = config('services.exchange_rate.provider', 'exchangerate_api_v6');

            if ($provider === 'exchangerate_api_v6') {
                $apiKey = config('services.exchange_rate.key');
                $endpointTemplate = config('services.exchange_rate.v6_endpoint', 'https://v6.exchangerate-api.com/v6/%s/latest/%s');

                if (empty($apiKey)) {
                    throw new \Exception("ExchangeRateService: API Key is missing.");
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

                    throw new \Exception("ExchangeRateService: Invalid response structure from API for base $base.");
                }

                throw new \Exception("ExchangeRateService: API request failed for base $base. Status: " . $resp->status() . " Body: " . $resp->body());
            }

            throw new \Exception("ExchangeRateService: Provider not supported or configured.");
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
            // ratesFromIdr maps IDR -> X (1 IDR = X Currency)
            // Amount_From / Rate(IDR->From) = Amount_IDR
            // Amount_IDR * Rate(IDR->To) = Amount_To
            // So: Amount * (Rate(IDR->To) / Rate(IDR->From))

            $idrPerFrom = $ratesFromIdr[$from];
            $idrPerTo = $ratesFromIdr[$to];

            if ($idrPerFrom == 0) return null;
            return $amount * ($idrPerTo / $idrPerFrom);
        }

        return null;
    }
}
