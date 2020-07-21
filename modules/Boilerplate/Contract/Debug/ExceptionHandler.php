<?php

namespace Modules\Boilerplate\Contract\Debug;

interface ExceptionHandler
{
    /**
     * Handle an exception.
     *
     * @param \Throwable|\Exception $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function handle($exception);
}
