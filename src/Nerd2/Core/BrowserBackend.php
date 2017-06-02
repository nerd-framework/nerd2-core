<?php

namespace Nerd2\Core;

class BrowserBackend implements Backend
{
    private static $instance = null;

    private function __construct() {}

    public static function getInstance(): Backend
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function isHeadersSent(): bool
    {
        return headers_sent();
    }

    public function sendHeader(string $name, string $value): void
    {
        header("$name: $value");
    }

    public function sendCookie(string $name, string $value): void
    {
        setcookie($name, $value);
    }

    public function sendResponseCode(int $responseCode): void
    {
        http_response_code($responseCode);
    }

    public function sendBody($body): void
    {
        echo $body;
    }
}
