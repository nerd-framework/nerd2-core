<?php

namespace Nerd2\Core;

use \Nerd2\Core\Exceptions\HttpException;

class Context
{
    private $request;
    private $response;
    private $app;
    private $state;

    public function __construct(Request $request, Response $response, Nerd $app)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = $response;

        $this->state = [];
    }

    public function throw(int $responseCode, $body = ''): void
    {
        throw new HttpException($responseCode, $body);
    }

    public function render(string $template, array $params = []): void
    {
        $render = $this->app->getService('render');
        $result = $render($template, $params);
        $this->response->setBody($result);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getApp(): Nerd
    {
        return $this->app;
    }

    public function getState(string $key)
    {
        return array_key_exists($key, $this->state) ? $this->state[$key] : null;
    }

    public function setState(string $key, $value): void
    {
        $this->state[$key] = $value;
    }
}
