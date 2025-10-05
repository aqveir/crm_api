<?php

namespace Modules\CloudTelephony\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TelephonyConfigurationException extends HttpException
{
    /**
     * Create a new rate limit exceeded exception instance.
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
        $message = 'EXCEPTION_TELEPHONY_CONFIGURATION';
        parent::__construct(429, $message ?: 'You have exceeded your rate limit.', $previous, $headers, $code);
    }
}
