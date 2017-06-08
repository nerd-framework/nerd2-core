<?php

namespace Nerd2\Core;

class Request implements \JsonSerializable
{
    private $method;
    private $path;
    private $headers;
    private $params;
    private $post;
    private $cookies;
    private $files;

    public function __construct(string $method, string $path, array $headers, array $params, array $post, array $cookies, array $files)
    {
        $this->method = $method;
        $this->path = $path;
        $this->headers = $headers;
        $this->params = $params;
        $this->post = $post;
        $this->cookies = $cookies;
        $this->files = $files;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getParam(string $name): string
    {
        return $this->params[$name] ?? null;
    }

    public function getPost(string $name): string
    {
        return $this->post[$name] ?? null;
    }

    public function getCookie(string $name): string
    {
        return $this->cookies[$name] ?? null;
    }

    public function getFile(string $name): array
    {
        return $this->files[$name] ?? null;
    }

    public function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    public static function capture()
    {
        $method = $_SERVER["REQUEST_METHOD"];
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $headers = getallheaders();
        $params = $_GET;
        $post = $_POST;
        $cookies = $_COOKIE;
        $files = $_FILES;

        return new self($method, $path, $headers, $params, $post, $cookies, $files);
    }

    public function jsonSerialize(): array
    {
        return [
            'method' => $this->method,
            'path' => $this->path,
            'params' => $this->params,
            'headers' => $this->headers,
        ];
    }
}
