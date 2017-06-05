<?php

namespace Nerd2\Module;

function module(string $module)
{
    $path = getCalledFrom(1);
    static $modules = [];
    $file = findPath($path . DIRECTORY_SEPARATOR . $module);
    $realpath = realpath($file);
    
    if (!array_key_exists($realpath, $modules)) {
        $modules[$realpath] = load($realpath);
    }

    return $modules[$realpath];
}

function load(string $__path)
{
    $module = (object) [];
    require($__path);
    return $module;
}

function findPath(string $module): string
{
    $tryFiles = [$module, "$module.php", "$module/index.php"];
    foreach ($tryFiles as $file) {
        if (file_exists($file)) {
            return $file;
        }
    }
    throw new \Exception("Module $module not found");
}

function getCalledFrom(int $pos): string
{
    $stack = debug_backtrace();
    return pathinfo($stack[$pos]['file'], PATHINFO_DIRNAME);
}