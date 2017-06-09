<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Context;
use \Nerd2\Core\Router\Route;

require_once('vendor/autoload.php');

$middleware = [
    Route::get('/', function (Context $context) {
        $context->render('index', ['name' => 'World']);
    })
];

$services = [
    new \Nerd2\Core\View\GenericView(__DIR__ . '/view', '.php')
];

$app = new Nerd($middleware, $services);

$app->run();
