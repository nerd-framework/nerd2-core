<?php

namespace Nerd2\Proxy;

class HelloWorld
{
    public function hello(string $name): string
    {
        return "Hello, $name! " . $this->bye($name);
    }

    protected function bye(string $name): string
    {
        return "Bye, $name!";
    }
}
