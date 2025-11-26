<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Set this to an IP or CIDR list, or '*' to trust all proxies.
     * You can also set the `TRUSTED_PROXIES` env var to override.
     *
     * @var array|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    // Use explicit flags for forwarded headers (compatible with installed Symfony)
    protected $headers = (
        Request::HEADER_FORWARDED
        | Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PREFIX
    );

    public function __construct()
    {
        // Allow configuring trusted proxies via environment variable.
        // Examples:
        // - TRUSTED_PROXIES=192.0.2.1
        // - TRUSTED_PROXIES=192.0.2.0/24,198.51.100.0/24
        // - TRUSTED_PROXIES=*  (trust all proxies)
        $value = env('TRUSTED_PROXIES', null);

        if ($value !== null && $value !== '') {
            // If wildcard, set to '*' so the base middleware trusts all.
            if ($value === '*') {
                $this->proxies = '*';
            } else {
                $this->proxies = array_map('trim', explode(',', $value));
            }
        }
    }
}
