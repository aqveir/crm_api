<?php

namespace Modules\Boilerplate\Contract\Http;

use Illuminate\Http\Request as IlluminateRequest;

interface Request
{
    /**
     * Create a new Boilerplate request instance from an Illuminate request instance.
     *
     * @param \Illuminate\Http\Request $old
     *
     * @return \Modules\Boilerplate\Http\Request
     */
    public function createFromIlluminate(IlluminateRequest $old);
}
