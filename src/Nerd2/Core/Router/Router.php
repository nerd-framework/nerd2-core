<?php

namespace Nerd2\Core\Router;

use \Closure;
use \Nerd2\Core\Context;
use function \Nerd2\Core\makeCascade;

class Router
{
    private $routes = [];

    public function get(string $route, Closure ...$actions): void
    {
        array_push($this->routes, Route::get($route, ...$actions));
    }

    public function post(string $route, Closure ...$actions): void
    {
        array_push($this->routes, Route::post($route, ...$actions));
    }

    public function put(string $route, Closure ...$actions): void
    {
        array_push($this->routes, Route::put($route, ...$actions));
    }

    public function delete(string $route, Closure ...$actions): void
    {
        array_push($this->routes, Route::delete($route, ...$actions));
    }
    
    public function any(string $route, Closure ...$actions): void
    {
        array_push($this->routes, Route::any($route, ...$actions));
    }

    public function __invoke(Context $context, Closure $next): void
    {
        $cascade = makeCascade($this->routes);
        $cascade($context, $next);
    }
}
