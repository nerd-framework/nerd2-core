<?php

namespace Nerd2\Core;

interface Backend
{
    public function isHeadersSent(): bool;

    public function getHeadersList(): array;

    public function sendHeader(string $name, string $value): void;

    public function sendCookie(string $name, string $value): void;

    public function sendResponseCode(int $responseCode): void;

    public function sendBody($body): void;
}
