<?php

namespace Nerd2\Core;

use \Closure;
use \Nerd2\Core\Context;

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
