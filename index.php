<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;
use \Nerd2\Core\BrowserBackend;

require_once('vendor/autoload.php');

$app = new Nerd();

$request = Request::capture();
$backend = BrowserBackend::getInstance();

$app->use(Route::get('/', function ($context, $next) {
    $context->response->body = 'Home';
}));

$app->use(Route::get('/greet/:name', function ($context) {
    $name = $context->request->params['name'];
    $context->response->body = "Hello, {$name}!";
}));

$app->use(Route::get('/error', function ($context) {
    throw new \RuntimeException('Runtime exception!');
}));

$app->use(Route::get('/redir', function ($context) {
    $context->response->redirect = '/greet/You';
}));

$app->use(Route::any('/echo', function ($context) {
    $context->response->body = $context->request;
}));

$app->handle($request, $backend);
