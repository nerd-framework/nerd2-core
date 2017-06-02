<?php

namespace Nerd2\Core\Exceptions;

class NerdException extends \Exception
{
    public function __construct(string $message, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
