<?php

namespace Nerd2\Core;

use \Nerd2\Core\Client;

class Response
{
    use \Nerd2\Core\Utils\AutoGetterSetter;

    protected static $_autoGetters = ['responseCode', 'headers', 'cookies'];
    protected static $_autoSetters = ['responseCode', 'body'];

    private const DEFAULT_RESPONSE_CODE = 200;

    private $responseCode = self::DEFAULT_RESPONSE_CODE;
    private $headers = [];
    private $cookies = [];
    private $body = '';

    public function sendTo(Backend $backend): void
    {
        $this->normalizeHeaders();
        $this->prepareResponse();

        $backend->sendResponseCode($this->responseCode);

        foreach ($this->headers as $name => $value) {
            $backend->sendHeader($name, $value);
        }

        foreach ($this->cookies as $name => $value) {
            $backend->sendCookie($name, $value);
        }

        $backend->sendBody($this->body);
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

    public function hasHeader($name): bool
    {
        return array_key_exists($name, $this->headers);
    }
}
