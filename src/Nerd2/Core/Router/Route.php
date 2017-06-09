<?php

namespace Nerd2\Core\Router;

use \Closure;
use \Nerd2\Core\Context;
use \Nerd2\Core\Exceptions\NerdException;
use function \Nerd2\Core\cascade;

class Route
{
    private $routeRegexp;
    private $methods;
    private $action;

    public function __construct(array $methods, string $route, \Closure ...$actions)
    {
        $this->checkRoute($route);

        $escapedRoute = $this->escapeSpecialSymbols($route);
        $convertedRoute = $this->convertParameters($escapedRoute);
        
        $this->routeRegexp = "~^$convertedRoute$~";

        $this->methods = $methods;
        $this->action = cascade($actions);
    }

    private function checkRoute(string $route): void
    {
        if (strlen($route) === 0) {
            throw new NerdException("Unexpected empty route");
        }

        if ($route[0] !== '/') {
            throw new NerdException("Routes must be prefixed by \"/\"");
        }
    }

    public function __invoke(Context $context, Closure $next): void
    {
        $request = $context->getRequest();

        if (!empty($this->methods) && !in_array($request->getMethod(), $this->methods)) {
            $next();
            return;
        }

        if (!preg_match($this->routeRegexp, $request->getPath(), $match)) {
            $next();
            return;
        }

        $params = $this->filterArgs(array_slice($match, 1));
        $context->getRequest()->addParams($params);

        call_user_func($this->action, $context, $next);
    }

    private function escapeSpecialSymbols(string $route): string
    {
        $specialSymbols = '.\\+*?[^]$~(){}=!<>|-';
        return implode('', array_map(function ($char) use ($specialSymbols) {
            return strpos($specialSymbols, $char) === false ? $char : '\\' . $char;
        }, str_split($route)));
    }

    private function convertParameters(string $route): string
    {
        $updatedRoute = preg_replace('/::([^\/]+)/', '(?P<$1>.+?)', $route);
        $updatedRoute = preg_replace('/:([^\/]+)/', '(?P<$1>[\w-]+)', $updatedRoute);
        $updatedRoute = preg_replace('/&([\w-_]+)/', '(?P<$1>[\d]+)', $updatedRoute);
        return $updatedRoute;
    }

    private function filterArgs(array $args): array
    {
        return array_filter($args, "is_string", ARRAY_FILTER_USE_KEY);
    }

    public static function get(string $route, Closure $action): Route
    {
        return new self(['HEAD', 'GET'], $route, $action);
    }

    public static function post(string $route, Closure $action): Route
    {
        return new self(['POST'], $route, $action);
    }

    public static function put(string $route, Closure $action): Route
    {
        return new self(['PUT'], $route, $action);
    }
    
    public static function delete(string $route, Closure $action): Route
    {
        return new self(['DELETE'], $route, $action);
    }

    public static function any(string $route, Closure $action): Route
    {
        return new self([], $route, $action);
    }
}
