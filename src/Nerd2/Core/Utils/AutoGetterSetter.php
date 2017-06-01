<?php

namespace Nerd2\Core\Utils;

trait AutoGetterSetter 
{
    public function __get($name)
    {
        if ($this->isInAutoGetters($name)) {
            return $this->$name;
        }

        $getter = self::toGetter($name);

        if ($this->isGetterExists($getter)) {
            return $this->$getter();
        }

        throw new \RuntimeException("Attribute ($name) doesn't exist or inaccessible");
    }

    public function __set($name, $value)
    {
        if ($this->isInAutoSetters($name)) {
            $this->$name = $value;
            return;
        }

        $setter = self::toSetter($name);

        if ($this->isSetterExists($setter)) {
            $this->$setter($value);
            return;
        }

        throw new \RuntimeException("Attribute ($name) doesn't exist or inaccessible");
    }

    private static function toGetter(string $name): string
    {
        return 'get' . ucfirst($name);
    }

    private static function toSetter(string $name): string
    {
        return 'set' . ucfirst($name);
    }

    private function isGetterExists($getter): bool
    {
        return method_exists($this, $getter);
    }

    public function isSetterExists($setter): bool
    {
        return method_exists($this, $getter);
    }

    public function isInAutoGetters($name): bool
    {
        return isset(static::$_autoGetters) && in_array($name, static::$_autoGetters);
    }

    public function isInAutoSetters($name): bool
    {
        return isset(static::$_autoSetters) && in_array($name, static::$_autoSetters);
    }
}
