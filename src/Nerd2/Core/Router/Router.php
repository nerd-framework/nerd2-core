<?php

namespace Nerd2\Core\Router;

use \Closure;
use \Nerd2\Core\Context;
use function \Nerd2\Core\cascade;

class Router
{
    private $routes;
    private $routerPrefix;

    public function __construct(array $routes = [], string $routerPrefix = '')
    {
        $this->routes = $routes;
        $this->routerPrefix = rtrim($routerPrefix, '/');
    }

    private function normalize(string $route): string
    {
        return "{$this->routerPrefix}{$route}";
    }

    public function get(string $route, Closure ...$actions): Router
    {
        array_push($this->routes, Route::get($this->normalize($route), ...$actions));
        return $this;
    }

    public function post(string $route, Closure ...$actions): Router
    {
        array_push($this->routes, Route::post($this->normalize($route), ...$actions));
        return $this;
    }

    public function put(string $route, Closure ...$actions): Router
    {
        array_push($this->routes, Route::put($this->normalize($route), ...$actions));
        return $this;
    }

    public function delete(string $route, Closure ...$actions): Router
    {
        array_push($this->routes, Route::delete($this->normalize($route), ...$actions));
        return $this;
    }
    
    public function any(string $route, Closure ...$actions): Router
    {
        array_push($this->routes, Route::any($this->normalize($route), ...$actions));
        return $this;
    }

    public function __invoke(Context $context, Closure $next): void
    {
        $cascade = cascade($this->routes);
        $cascade($context, $next);
    }
}
