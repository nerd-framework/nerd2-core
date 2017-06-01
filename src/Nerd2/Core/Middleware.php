<?php

namespace Nerd2\Core;

use \Closure;

interface Middleware
{
    public function run(Context $context, ?Closure $next): void;
}
