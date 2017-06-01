<?php

namespace Nerd2\Core;

class Request
{
    public $method;
    public $path;
    public $params;
    public $headers;

    public function __construct(
        string $method = 'GET', 
        string $path = '/', 
        array $params = [],
        array $headers = []
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->params = $params;
        $this->headers = $headers;
    }

    public static function capture()
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        return new self($method, $path);
    }
}
