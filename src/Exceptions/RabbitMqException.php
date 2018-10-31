<?php

namespace Aixue\Tools\Exceptions;

use Throwable;

/**
 * Class RabbitMqException
 * @package Aixue\Tools\Exceptions
 */
class RabbitMqException extends \Exception
{
    /**
     * RabbitMqException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 1, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
