<?php

namespace Nerd2\Core;

class Client
{
    private $statusConsumer;
    private $headersConsumer;
    private $bodyConsumer;

    public function __construct(\Closure $statusConsumer, \Closure $headersConsumer, \Closure $bodyConsumer)
    {
        $this->statusConsumer = $statusConsumer;
        $this->headersConsumer = $headersConsumer;
        $this->bodyConsumer = $bodyConsumer;
    }

    public function sendStatus(int $status)
    {
        call_user_func($this->statusConsumer, $status);
    }

    public function sendHeaders(array $headers)
    {
        call_user_func($this->headersConsumer, $headers);
    }

    public function sendCookies(array $cookies)
    {
        //
    }

    public function sendBody($body)
    {
        call_user_func($this->bodyConsumer, $body);
    }

    static public function current()
    {
        $statusConsumer = function ($status) 
        {
            http_response_code($status);
        };

        $headersConsumer = function (array $headers) 
        {
            foreach ($headers as $key => $value)
            {
                header("${key}: ${value}");
            }
        };

        $bodyConsumer = function ($body)
        {
            echo $body;
        };

        return new self($statusConsumer, $headersConsumer, $bodyConsumer);
    }
}
