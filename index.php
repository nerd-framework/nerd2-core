<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Context;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;
use \Nerd2\Core\Router\Router;
use \Nerd2\Core\Router\CrudController;
use \Nerd2\Core\BrowserBackend;

use \Nerd2\Core\Middleware\PhpViewEngine;

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
