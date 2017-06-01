<?php

namespace Nerd2\Core;

use \Nerd2\Core\Client;

class Response
{
    private const DEFAULT_STATUS = 200;
    private const DEFAULT_THROW_STATUS = 500;
    private const DEFAULT_CONTENT_TYPE = 'text/plain';

    public $status;
    public $body;
    public $cookies;
    public $headers;

    public function __construct($body = '', int $status = self::DEFAULT_STATUS)
    {
        $this->status = $status;
        $this->body = $body;
        $this->cookies = [];
        $this->headers = [];
    }

    public function send(Client $client): void
    {
        $this->normalizeHeaders();
        $this->prepareResponse();

        $client->sendStatus($this->status);
        $client->sendHeaders($this->headers);
        $client->sendCookies($this->cookies);
        $client->sendBody($this->body);
    }

    public function throw(int $status = self::DEFAULT_THROW_STATUS, $body = null): void
    {
        throw new ApplicationException($status, $body);
    }

    private function prepareResponse(): void
    {
        if (is_array($this->body) && !isset($this->headers['Content-Type'])) {
            $this->headers['Content-Type'] = 'application/json';
        }
    }

    private function normalizeHeaders(): void
    {
        $keys = array_keys($this->headers);
        $this->headers = array_reduce($keys, function ($acc, $key) {
            $acc[$this->normalizeHeader($key)] = $this->headers[$key];
            return $acc;
        }, []);
    }

    private function normalizeHeader($header): string 
    {
        return implode('-', array_map('ucfirst', explode('-', $header)));
    }

    public function hasHeader($header): bool
    {
        return array_key_exists($header, $this->headers);
    }

    static public function empty(): self
    {
        return new self();
    }
}
