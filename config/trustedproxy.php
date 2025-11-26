<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Here you may specify the proxies that should be trusted by your
    | application. If your application is behind a load balancer or proxy,
    | set the `TRUSTED_PROXIES` env var to the proxy IP(s) or `*` to trust
    | all proxies. This file is read by the framework's TrustProxies
    | middleware.
    |
    */

    'proxies' => env('TRUSTED_PROXIES', null),

    /*
    |--------------------------------------------------------------------------
    | Trusted Headers
    |--------------------------------------------------------------------------
    |
    | Specify which headers should be used to detect proxy information.
    | Use the header string names like 'HEADER_X_FORWARDED_FOR' (the
    | middleware will map these to the appropriate Request constants).
    |
    */

    'headers' => env('TRUSTED_PROXY_HEADERS', 'HEADER_X_FORWARDED_FOR'),
];
