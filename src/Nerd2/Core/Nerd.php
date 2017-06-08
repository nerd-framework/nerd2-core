<?php

namespace Nerd2\Core;

use \Closure;
use \Exception;
use \Nerd2\Core\Exceptions\NerdException;
use \Nerd2\Core\Exceptions\HttpException;

class Nerd
{
    private $middleware;
    private $config;
    private $services;

    public function __construct(array $middleware = [], array $services = [], array $config = [])
    {
        $this->middleware = $middleware;
        $this->config = $config;
        $this->services = [];

        $this->loadServices($services);
    }

    private function loadServices(array $services): void
    {
        array_walk($services, function (callable $service) {
            $service($this);
        });
    }

    public function registerService(string $name, Closure $service): void
    {
        $this->services[$name] = $service;
    }

    public function getService(string $name)
    {
        return $this->services[$name];
    }

    public function getSetting($path, $default = null)
    {
        $parts = explode('.', $path);
        return array_reduce($parts, function ($acc, $part) {
            return (!is_null($acc) && array_key_exists($part, $acc)) ? $acc[$part] : null;
        }, $this->config) ?: $default;
    }

    public function run(): void
    {
        $request = request();
        $response = response();

        $this->proceed($request, $response);

        $response->prepare($request);
        $response->sendTo(browser());
    }

    public function proceed(Request $request, Response $response): void
    {
        $context = new Context($request, $response, $this);

        try {
            $this->runMiddleware($context);
        } catch (HttpException $e) {
            $context->getResponse()->setResponseCode($e->getResponseCode());
            $context->getResponse()->setBody($e->getBody());
        } catch (Exception | NerdException $e) {
            $context->getResponse()->setResponseCode(500);
        }
    }

    private function runMiddleware(Context $context): void
    {
        $defaultMiddleware = function () use ($context) {
            $context->getResponse()->setResponseCode(404);
        };

        $middleware = $this->middleware;

        $cascade = makeCascade($middleware);

        $this->runSilently($cascade, $context, $defaultMiddleware);
    }

    private function runSilently(Closure $function, ...$args): void
    {
        ob_start();
        $function(...$args);
        $side = ob_get_clean();

        if (strlen($side) > 0) {
            throw new NerdException("Side-effect body output detected");
        }
    }
}
