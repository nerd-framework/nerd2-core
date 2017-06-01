<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;

require_once('vendor/autoload.php');

$app = new Nerd();

$app->use(new Route('/', function ($ctx) {
    $ctx->response->body = 'Home';
}));

$app->use(new Route('/hello/:name', function ($ctx) {
    $name = $ctx->request->params['name'];
    $ctx->response->body = "Hello, ${name}!";
}));

$app->use(new Route('/error', function ($ctx) {
    $ctx->response->throw(400, 'Oops!');
}));

$app->run(Request::capture());
