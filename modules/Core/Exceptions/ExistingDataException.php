<?php

namespace Modules\Core\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExistingDataException extends HttpException
{
    /**
     * Create existing data exception instance.
     *
     * @param string     $message
     * @param \Exception $previous
     * @param array      $headers
     * @param int        $code
     *
     * @return void
     */
    public function __construct(Exception $previous = null, $headers = [], $code = 0)
    {
        $message = 'EXCEPTION_EXISTING_DATA';
        parent::__construct(429, $message ?: 'The data already exists.', $previous, $headers, $code);
    }
}
