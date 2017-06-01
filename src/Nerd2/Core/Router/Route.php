<?php

namespace Nerd2\Core\Router;

use \Closure;
use \Nerd2\Core\Context;

class Route
{
    private $pattern;
    private $callback;

    public function __construct(string $pattern, \Closure $callback)
    {
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    public function __invoke(Context $context, Closure $next): void
    {
        if ($context->request->path == $this->pattern) {
            call_user_func($this->callback, $context);
            return;
        }
    
        $next();
    }
}
