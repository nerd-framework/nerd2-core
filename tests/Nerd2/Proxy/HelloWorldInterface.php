<?php

namespace Nerd2\Proxy;

interface HelloWorldInterface
{
    public function hello(string $name): string;

    public function bye(string $name): string;
}