<?php

namespace Nerd2\Core\Exceptions;

class HttpException extends \Exception
{
    use \Nerd2\Core\Utils\AutoGetterSetter;

    protected static $_autoGetters = ['responseCode', 'body'];

    private $responseCode;
    private $body;

    public function __construct($responseCode = 500, $body = '')
    {
        parent::__construct("HttpException(code={$this->responseCode})");

        $this->responseCode = $responseCode;
        $this->body = $body;
    }
}
