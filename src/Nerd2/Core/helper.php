<?php

namespace Nerd2\Core;

use \Closure;

function makeCascade(array $middleware): Closure
{
    return function (Context $context, Closure $next) use ($middleware) {
        call_user_func(array_reduce(array_reverse($middleware), function ($next, $prev) use ($context) {
            return function () use ($next, $prev, $context) {
                $prev($context, $next);
            };
        }, $next));
    };
}

function get(string $key, array $array)
{
    return array_key_exists($key, $array) ? $array[$key] : null;
}

function request(): Request
{
    return Request::capture();
}

function response(): Response
{
    return Response::create();
}

function browser(): Backend
{
    return BrowserBackend::getInstance();
}
