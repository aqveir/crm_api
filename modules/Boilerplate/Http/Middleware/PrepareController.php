<?php

namespace Modules\Boilerplate\Http\Middleware;

use Closure;
use Modules\Boilerplate\Routing\Router;

class PrepareController
{
    /**
     * Boilerplate router instance.
     *
     * @var \Modules\Boilerplate\Routing\Router
     */
    protected $router;

    /**
     * Create a new prepare controller instance.
     *
     * @param \Modules\Boilerplate\Routing\Router $router
     *
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Handle the request.
     *
     * @param \Modules\Boilerplate\Http\Request $request
     * @param \Closure                $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // To prepare the controller all we need to do is call the current method on the router to fetch
        // the current route. This will create a new Modules\Boilerplate\Routing\Route instance and prepare the
        // controller by binding it as a singleton in the container. This will result in the
        // controller only be instantiated once per request.
        $this->router->current();

        return $next($request);
    }
}
