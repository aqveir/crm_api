<?php

namespace Modules\Core\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DuplicateDataException extends HttpException
{
    /**
     * Create duplicate data exception instance.
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
        $message = 'EXCEPTION_DUPLICATE_DATA';
        parent::__construct(429, $message ?: 'You have a duplicate data.', $previous, $headers, $code);
    }
}
