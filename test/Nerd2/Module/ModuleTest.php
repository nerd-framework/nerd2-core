<?php

namespace Nerd2\Module;

use \PHPUnit\Framework\TestCase;

use function \Nerd2\Module\module;

class ModuleTest extends TestCase
{
    public function testModuleLoad()
    {
        $module = module("../../fixture/module1");
    }
}
