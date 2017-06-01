<?php

namespace Nerd2\Core;

use \Nerd2\Core\Client;

class Response
{
    private const DEFAULT_STATUS = 200;

    public $status;
    public $body;
    public $cookies;
    public $headers;

    public function __construct(int $status, $body)
    {
        $this->status = $status;
        $this->body = $body;
        $this->cookies = [];
        $this->headers = [];
    }

    public function send(Client $client)
    {
        $client->sendStatus($this->status);
        $client->sendHeaders($this->headers);
        $client->sendCookies($this->cookies);
        $client->sendBody($this->body);
    }

    static public function empty()
    {
        return new self(self::DEFAULT_STATUS, '');
    }
}
