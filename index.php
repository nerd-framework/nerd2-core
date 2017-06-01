<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;
use \Nerd2\Core\Backend;

require_once('vendor/autoload.php');

$app = new Nerd();

$request = Request::capture();
$backend = new class implements Backend
{
    public function isHeadersSent(): bool
    {
        return headers_sent();
    }

    public function sendHeader(string $name, string $value): void
    {
        header("$name: $value");
    }

    public function sendCookie(string $name, string $value): void
    {
        setcookie($name, $value);
    }

    public function sendResponseCode(int $responseCode): void
    {
        http_response_code($responseCode);
    }

    public function sendBody($body): void
    {
        echo $body;
    }
};

$app->use(new Route('/', function ($context, $next) {
    $context->response->body = 'Home';
}));

$app->use(new Route('/greet/:name', function ($context, $next) {
    $name = $context->request->params['name'];
    $context->response->body = "Hello, {$name}!";
}));

$app->use(new Route('/error', function ($context, $next) {
    throw new \RuntimeException('Runtime exception!');
}));

$app->handle($request, $backend);
