<?php

namespace Nerd2\Proxy;

interface ProxyHandler
{
    public function invoke(string $name, array $args);
}
