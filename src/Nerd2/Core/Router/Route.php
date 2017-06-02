<?php

namespace Nerd2\Core\Router;

use \Closure;
use \Nerd2\Core\Context;
use \Nerd2\Core\Exceptions\NerdException;
use function \Nerd2\Core\makeCascade;

class Route
{
    private $routeRegexp;
    private $action;

    public function __construct(string $route, \Closure ...$actions)
    {
        $this->checkRoute($route);

        $escapedRoute = $this->escapeSpecialSymbols($route);
        $convertedRoute = $this->convertParameters($escapedRoute);
        
        $this->routeRegexp = "~^$convertedRoute$~";
        $this->callback = makeCascade($actions);
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
        if (preg_match($this->routeRegexp, $context->request->path, $match)) {
            $params = $this->filterArgs(array_slice($match, 1));
            $context->request->mergeParams($params);
            call_user_func($this->callback, $context, $next);
            return;
        }
    
        $next();
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
}
