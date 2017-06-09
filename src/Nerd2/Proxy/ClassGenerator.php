<?php

namespace Nerd2\Proxy;

class ClassGenerator
{
    public function generate(string $className, array $interfaceList, array $methodList): string
    {
        ob_start();
        require(__DIR__ . DIRECTORY_SEPARATOR . 'class.php');
        return ob_get_clean();
    }
}
