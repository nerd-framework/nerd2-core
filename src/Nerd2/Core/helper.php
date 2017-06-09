<?php

namespace Nerd2\Core;

use \Closure;

function cascade(array $middleware): Closure
{
    return function (Context $context, Closure $next) use ($middleware) {
        call_user_func(array_reduce(array_reverse($middleware), function ($next, $prev) use ($context) {
            return function () use ($next, $prev, $context) {
                $prev($context, $next);
            };
        }, $next));
    };
}

function relative(string $path): string
{
    $stack = debug_backtrace();
    $calledFrom = pathinfo($stack[0]['file'], PATHINFO_DIRNAME);
    return $calledFrom . DIRECTORY_SEPARATOR . $path;
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
