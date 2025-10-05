<?php

namespace Modules\Boilerplate\Event;

use Modules\Boilerplate\Http\Request;
use Illuminate\Contracts\Container\Container;

class RequestWasMatched
{
    /**
     * Request instance.
     *
     * @var \Modules\Boilerplate\Http\Request
     */
    public $request;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    public $app;

    /**
     * Create a new request was matched event.
     *
     * @param \Modules\Boilerplate\Http\Request                   $request
     * @param \Illuminate\Contracts\Container\Container $app
     *
     * @return void
     */
    public function __construct(Request $request, Container $app)
    {
        $this->request = $request;
        $this->app = $app;
    }
}
