<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;
use \Nerd2\Core\Router\Router;
use \Nerd2\Core\BrowserBackend;

require_once('vendor/autoload.php');

Nerd::init(function (Nerd $app)
{
    $app->use( 
        
        (new Router())

            ->get('/', function ($context, $next) {
                $context->response->body = 'Home';
            })

            ->get('/greet/:name', function ($context) {
                $name = $context->request->params['name'];
                $context->response->body = "Hello, {$name}!";
            })

            ->get('/error', function ($context) {
                throw new \RuntimeException('Runtime exception!');
            })

            ->get('/redir', function ($context) {
                $context->response->redirect = '/greet/You';
            })

            ->any('/echo', function ($context) {
                $context->response->body = $context->request;
            })
    );

    $app->use(

        (new Router('/users'))

            ->get('/', function ($context) {
                $context->response->body = 'Users List';
            })

            ->get('/:id', function ($context) {
                $id = $context->request->params['id'];
                $context->response->body = 'Get User ' . $id;
            })

            ->post('/', function ($context) {
                $context->response->body = 'New User';
            })

            ->put('/:id', function ($context) {
                $id = $context->request->params['id'];
                $context->response->body = 'Update User ' . $id;
            })

            ->delete('/:id', function ($context) {
                $id = $context->request->params['id'];
                $context->response->body = 'Delete User ' . $id;
            })

    );
});
