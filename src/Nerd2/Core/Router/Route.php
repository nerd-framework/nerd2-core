<?php

namespace Nerd2\Core\Router;

use \Closure;
use \Nerd2\Core\Context;

class Route
{
    private $routeRegexp;
    private $action;

    public function __construct(string $route, \Closure $action)
    {
        $escapedRoute = $this->escapeSpecialSymbols($route);
        $convertedRoute = $this->convertParameters($escapedRoute);
        
        $this->routeRegexp = "~^$convertedRoute$~";
        $this->callback = $action;
    }

    public function __invoke(Context $context, Closure $next): void
    {
        if (preg_match($this->routeRegexp, $context->request->path, $match)) {
            $params = $this->filterArgs(array_slice($match, 1));
            $newContext = clone $context;
            $newContext->request->params = $params;
            call_user_func($this->callback, $newContext);
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
        $isNumeric = array_reduce(array_keys($args), function ($acc, $item) {
            return $acc && is_int($item);
        }, true);
        $filter = $isNumeric ? "is_int" : "is_string";
        return array_filter($args, $filter, ARRAY_FILTER_USE_KEY);
    }
}
