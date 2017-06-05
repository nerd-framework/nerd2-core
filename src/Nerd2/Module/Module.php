<?php

namespace Nerd2\Module;

function module(string $path): stdClass
{
    static $modules = [];
    $file = findPath($path);
    $realpath = realpath($file);
    
    if (!array_key_exists($realpath, $modules)) {
        $modules[$realpath] = load($realpath);
    }

    return $modules[$realpath];
}

function load(string $__path): stdClass
{
    require($__path);
    return $module;
}

function findPath(string $path): string
{
    $tryFiles = [$path, "$path.php", "$path/index.php"];
    foreach ($tryFiles as $file) {
        if (file_exists($file)) {
            return $file;
        }
    }
    throw new \Exception("Module $path not found");
}
