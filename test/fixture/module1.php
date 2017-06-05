<?php

use function \Nerd2\Module\module;

$module2 = module('module2');

$module = (object) [
    'foo' => 'bar', 
    'run' => function () {
        return 'baz';
    },
    'func' => $module2->func
];
