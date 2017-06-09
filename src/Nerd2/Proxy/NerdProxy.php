<?php

namespace Nerd2\Proxy;

use Closure;

class NerdProxy
{
    private static $lastProxyId = 0;

    private $proxyHandler;
    private $interfaces;
    private $parentClass;
    private $className;
    private $proxyClass;

    public function __construct(Closure $proxyHandler, array $interfaces = [], $parentClass = null)
    {
        $this->proxyHandler = $proxyHandler;
        $this->interfaces = $interfaces;
        $this->parentClass = $parentClass;
        $this->className = '__ProxyClass' . (self::$lastProxyId++);

        $this->proxyClass = $this->makeClass(
            $this->className,
            $this->interfaces,
            $this->parentClass,
            $this->mergeAllMethods()
        );
    }

    /**
     * Generates proxy object for a given object.
     *
     * @param object $object
     * @param Closure $objectProxyHandler
     * @return object
     */
    public static function forObject($object, Closure $objectProxyHandler)
    {
        $interfaces = array_map(function (\ReflectionClass $interface) {
            return $interface->getName();
        }, (new \ReflectionClass($object))->getInterfaces());

        $generator = new self(function (string $name, array $args) use ($objectProxyHandler, $object) {
            $method = new \ReflectionMethod($object, $name);
            return $objectProxyHandler($name, $args, $method);
        }, $interfaces, get_class($object));

        return $generator->newInstance();
    }

    public function newInstance()
    {
        $this->evalClass();

        return new $this->className($this->proxyHandler);
    }

    private function makeClass(string $className, array $interfaceList, $parentClass, array $methodList): string
    {
        ob_start();
        require(__DIR__ . DIRECTORY_SEPARATOR . 'class.php');
        return ob_get_clean();
    }

    public function evalClass(): void
    {
        if (!class_exists($this->className)) {
            eval($this->proxyClass);
        }
    }

    private function mergeAllMethods(): array
    {
        $parentClassMethods = is_null($this->parentClass)
            ? []
            : $this->getClassMethods($this->parentClass);

        return array_merge(
            $parentClassMethods,
            ...array_map([$this, 'getClassMethods'], $this->interfaces)
        );
    }

    private function getClassMethods(string $className): array
    {
        $class = new \ReflectionClass($className);
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
