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
    public function __construct(Exception $previous = null, int $code = 0, array $headers = [])
    {
        $message = 'EXCEPTION_DUPLICATE_DATA';
        parent::__construct(400, $message, $previous, $headers, $code);
    }

} //Class ends
