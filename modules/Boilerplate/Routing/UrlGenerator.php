<?php

namespace Modules\Boilerplate\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator as IlluminateUrlGenerator;

class UrlGenerator extends IlluminateUrlGenerator
{
    /**
     * Array of route collections.
     *
     * @var array
     */
    protected $collections;

    /**
     * Create a new URL generator instance.
     *
     * @param \Modules\Boilerplate\Http\Request $request
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->setRequest($request);
    }

    /**
     * Set the routes to use from the version.
     *
     * @param string $version
     *
     * @return \Modules\Boilerplate\Routing\UrlGenerator
     */
    public function version($version)
    {
        $this->routes = $this->collections[$version];

        return $this;
    }

    /**
     * Set the route collection instance.
     *
     * @param array $collections
     */
    public function setRouteCollections(array $collections)
    {
        $this->collections = $collections;
    }
}
