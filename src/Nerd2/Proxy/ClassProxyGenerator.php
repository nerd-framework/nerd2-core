<?php

namespace Nerd2\Proxy;

class ClassProxyGenerator
{
    private static $lastProxyId = 0;

    private $proxyHandler;
    private $interfaces;
    private $className;

    public function __construct(ProxyHandler $proxyHandler, array $interfaces = [])
    {
        $this->proxyHandler = $proxyHandler;
        $this->interfaces = $interfaces;
        $this->className = '__ProxyClass' . (self::$lastProxyId++);

        $generator = new ClassGenerator();

        $generatedCode = $generator->generate($this->className, $this->interfaces, $this->getAllMethods());

        eval($generatedCode);

    }

    public function newInstance()
    {
        return new $this->className($this->proxyHandler);
    }

    private function getAllMethods(): array
    {
        return array_merge([], ...array_map([$this, 'getMethods'], $this->interfaces));
    }

    private function getMethods(string $interface): array
    {
        $class = new \ReflectionClass($interface);
        return array_map(function (\ReflectionMethod $method) {
            $name = $method->getName();
            $args = array_map([$this, 'renderParameter'], $method->getParameters());
            $return = $method->hasReturnType() ? strval($method->getReturnType()) : '';
            return compact('name', 'args', 'return');
        }, $class->getMethods());
    }

    private function renderParameter(\ReflectionParameter $parameter): string
    {
        return ($parameter->hasType()
            ? strval($parameter->getType()) . ' '
            : ''
        ) . '$' . $parameter->getName();
    }
}
