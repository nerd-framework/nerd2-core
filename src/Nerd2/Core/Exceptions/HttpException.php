<?php

namespace Nerd2\Core\Exceptions;

class HttpException extends \Exception
{
    private $responseCode;
    private $body;

    public function __construct($responseCode = 500, $body = '')
    {
        parent::__construct("HttpException(code={$this->responseCode})");

        $this->responseCode = $responseCode;
        $this->body = $body;
    }

    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
