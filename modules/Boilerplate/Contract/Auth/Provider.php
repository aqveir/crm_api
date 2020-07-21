<?php

namespace Modules\Boilerplate\Contract\Auth;

use Modules\Boilerplate\Routing\Route;
use Illuminate\Http\Request;

interface Provider
{
    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Modules\Boilerplate\Routing\Route $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route);
}
