<?php

namespace Modules\Boilerplate\Http\Middleware;

use Closure;
use Modules\Boilerplate\Routing\Router;
use Modules\Boilerplate\Auth\Auth as Authentication;

class Auth
{
    /**
     * Router instance.
     *
     * @var \Modules\Boilerplate\Routing\Router
     */
    protected $router;

    /**
     * Authenticator instance.
     *
     * @var \Modules\Boilerplate\Auth\Auth
     */
    protected $auth;

    /**
     * Create a new auth middleware instance.
     *
     * @param \Modules\Boilerplate\Routing\Router $router
     * @param \Modules\Boilerplate\Auth\Auth      $auth
     *
     * @return void
     */
    public function __construct(Router $router, Authentication $auth)
    {
        $this->router = $router;
        $this->auth = $auth;
    }

    /**
     * Perform authentication before a request is executed.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $this->router->getCurrentRoute();

        if (! $this->auth->check(false)) {
            $this->auth->authenticate($route->getAuthenticationProviders());
        }

        return $next($request);
    }
}
