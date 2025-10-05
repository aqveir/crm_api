<?php

namespace Modules\Boilerplate\Http\Middleware;

use Closure;
use Modules\Boilerplate\Http\Response;
use Modules\Boilerplate\Routing\Router;
use Modules\Boilerplate\Http\InternalRequest;
use Modules\Boilerplate\Http\RateLimit\Handler;
use Modules\Boilerplate\Exception\RateLimitExceededException;

class RateLimit
{
    /**
     * Router instance.
     *
     * @var \Modules\Boilerplate\Routing\Router
     */
    protected $router;

    /**
     * Rate limit handler instance.
     *
     * @var \Modules\Boilerplate\Http\RateLimit\Handler
     */
    protected $handler;

    /**
     * Create a new rate limit middleware instance.
     *
     * @param \Modules\Boilerplate\Routing\Router         $router
     * @param \Modules\Boilerplate\Http\RateLimit\Handler $handler
     *
     * @return void
     */
    public function __construct(Router $router, Handler $handler)
    {
        $this->router = $router;
        $this->handler = $handler;
    }

    /**
     * Perform rate limiting before a request is executed.
     *
     * @param \Modules\Boilerplate\Http\Request $request
     * @param \Closure                $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request instanceof InternalRequest) {
            return $next($request);
        }

        $route = $this->router->getCurrentRoute();

        if ($route->hasThrottle()) {
            $this->handler->setThrottle($route->getThrottle());
        }

        $this->handler->rateLimitRequest($request, $route->getRateLimit(), $route->getRateLimitExpiration());

        if ($this->handler->exceededRateLimit()) {
            throw new RateLimitExceededException('You have exceeded your rate limit.', null, $this->getHeaders());
        }

        $response = $next($request);

        if ($this->handler->requestWasRateLimited()) {
            return $this->responseWithHeaders($response);
        }

        return $response;
    }

    /**
     * Send the response with the rate limit headers.
     *
     * @param \Modules\Boilerplate\Http\Response $response
     *
     * @return \Modules\Boilerplate\Http\Response
     */
    protected function responseWithHeaders($response)
    {
        foreach ($this->getHeaders() as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }

    /**
     * Get the headers for the response.
     *
     * @return array
     */
    protected function getHeaders()
    {
        return [
            'X-RateLimit-Limit' => $this->handler->getThrottleLimit(),
            'X-RateLimit-Remaining' => $this->handler->getRemainingLimit(),
            'X-RateLimit-Reset' => $this->handler->getRateLimitReset(),
        ];
    }
}
