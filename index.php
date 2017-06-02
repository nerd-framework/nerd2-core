<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;
use \Nerd2\Core\BrowserBackend;

require_once('vendor/autoload.php');

$app = new Nerd();

$request = Request::capture();
$backend = BrowserBackend::getInstance();

$app->use(new Route('/', function ($context, $next) {
    $context->response->body = 'Home';
}));

$app->use(new Route('/greet/:name', function ($context) {
    $name = $context->request->params['name'];
    $context->response->body = "Hello, {$name}!";
}));

$app->use(new Route('/error', function ($context) {
    throw new \RuntimeException('Runtime exception!');
}));

$app->use(new Route('/redir', function ($context) {
    $context->response->redirect = '/greet/You';
}));

$app->handle($request, $backend);
