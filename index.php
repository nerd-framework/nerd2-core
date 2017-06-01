<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;

require_once('vendor/autoload.php');

$app = new Nerd();

$app->use(function ($ctx, $next) {
    $ctx->response->body = 'Hello, World!';
});

$app->run(Request::capture());
