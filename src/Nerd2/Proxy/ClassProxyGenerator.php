<?php

namespace Nerd2\Proxy;

use Closure;

class ClassProxyGenerator
{
    private static $lastProxyId = 0;

    private $proxyHandler;
    private $interfaces;
    private $className;
    private $generatedCode;

    public function __construct(Closure $proxyHandler, array $interfaces = [])
    {
        $this->proxyHandler = $proxyHandler;
        $this->interfaces = $interfaces;
        $this->className = '__ProxyClass' . (self::$lastProxyId++);

        $this->generatedCode = $this->generateClassCode(
            $this->className,
            $this->interfaces,
            $this->mergeAllInterfacesMethods()
        );
    }

    public function newInstance()
    {
        $this->evalGeneratedCode();

        return new $this->className($this->proxyHandler);
    }

    public function generateClassCode(string $className, array $interfaceList, array $methodList): string
    {
        ob_start();
        require(__DIR__ . DIRECTORY_SEPARATOR . 'class.php');
        return ob_get_clean();
    }

    public function evalGeneratedCode(): void
    {
        if (!class_exists($this->className)) {
            eval($this->generatedCode);
        }
    }

    private function mergeAllInterfacesMethods(): array
    {
        return array_merge([], ...array_map([$this, 'getInterfaceMethods'], $this->interfaces));
    }

    private function getInterfaceMethods(string $interface): array
    {
        $class = new \ReflectionClass($interface);
        return array_map(function (\ReflectionMethod $method) {
            $name = $method->getName();
            $args = $this->renderParameters($method);
            $return = $this->renderReturnType($method);
            return compact('name', 'args', 'return');
        }, $class->getMethods());
    }

    private function renderParameters(\ReflectionMethod $method): array
    {
        return array_map([$this, 'renderParameter'], $method->getParameters());
    }

    private function renderParameter(\ReflectionParameter $parameter): string
    {
        return $parameter->hasType()
            ? "{$parameter->getType()} \${$parameter->getName()}"
            : "\${$parameter->getName()}";
    }

    private function renderReturnType(\ReflectionMethod $method): string
    {
        return $method->hasReturnType()
            ? "{$method->getReturnType()}"
            : "";
    }
}
