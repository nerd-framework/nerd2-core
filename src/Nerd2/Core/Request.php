<?php

namespace Nerd2\Core;

class Request
{
    public $method;
    public $path;
    public $query;
    public $headers;
    public $params;

    public function __construct(
        string $method = 'GET', 
        string $path = '/', 
        array $query = [],
        array $headers = []
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->query = $query;
        $this->headers = $headers;

        $this->params = [];
    }

    public static function capture()
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        return new self($method, $path);
    }
}
