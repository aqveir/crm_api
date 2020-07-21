<?php

namespace Modules\Boilerplate\Contract\Http\RateLimit;

use Modules\Boilerplate\Http\Request;
use Illuminate\Container\Container;

interface HasRateLimiter
{
    /**
     * Get rate limiter callable.
     *
     * @param \Illuminate\Container\Container $app
     * @param \Modules\Boilerplate\Http\Request         $request
     *
     * @return string
     */
    public function getRateLimiter(Container $app, Request $request);
}
