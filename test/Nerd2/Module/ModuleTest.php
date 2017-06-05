<?php

namespace Nerd2\Module;

use \PHPUnit\Framework\TestCase;

use function \Nerd2\Module\module;

class ModuleTest extends TestCase
{
    public function testModuleLoad()
    {
        $module = module("../../fixture/module1");
        $this->assertEquals('bar', $module->foo);
        $this->assertEquals('baz', ($module->run)());
    }

    public function testModulesAreSame()
    {
        $module1 = module("../../fixture/module1");
        $module2 = module("../../fixture/module2");

        $this->assertSame($module1->func, $module2->func);
    }

    public function testModulesUsingIndex()
    {
        $module3 = module("../../fixture/module3");

        $this->assertEquals('abc', $module3->abc);
    }
}
