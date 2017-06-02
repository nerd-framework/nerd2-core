<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;
use \Nerd2\Core\Router\Router;
use \Nerd2\Core\BrowserBackend;

require_once('vendor/autoload.php');

Nerd::init(function (Nerd $app)
{
    $router = new Router();

    $router->get('/', function ($context, $next) {
        $context->response->body = 'Home';
    });

    $router->get('/greet/:name', function ($context) {
        $name = $context->request->params['name'];
        $context->response->body = "Hello, {$name}!";
    });

    $router->get('/error', function ($context) {
        throw new \RuntimeException('Runtime exception!');
    });

    $router->get('/redir', function ($context) {
        $context->response->redirect = '/greet/You';
    });

    $router->any('/echo', function ($context) {
        $context->response->body = $context->request;
    });

    $app->use($router);

    $userRouter = new Router('/users');

    $userRouter->get('/', function ($context) {
        $context->response->body = 'Users List';
    });

    $userRouter->get('/:id', function ($context) {
        $id = $context->request->params['id'];
        $context->response->body = 'Get User ' . $id;
    });

    $userRouter->post('/', function ($context) {
        $context->response->body = 'New User';
    });

    $userRouter->put('/:id', function ($context) {
        $id = $context->request->params['id'];
        $context->response->body = 'Update User ' . $id;
    });

    $userRouter->delete('/:id', function ($context) {
        $id = $context->request->params['id'];
        $context->response->body = 'Delete User ' . $id;
    });

    $app->use($userRouter);
});
