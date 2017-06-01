<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;

require_once('vendor/autoload.php');

$app = new Nerd();

$app->use(new Route('/', function ($ctx) {
    $ctx->response->body = 'Home';
}));

$app->use(new Route('/hello', function ($ctx) {
    $ctx->response->body = 'Hello, World!';
}));

$app->run(Request::capture());
