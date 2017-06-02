<?php

use \Nerd2\Core\Nerd;
use \Nerd2\Core\Context;
use \Nerd2\Core\Request;
use \Nerd2\Core\Router\Route;
use \Nerd2\Core\Router\Router;
use \Nerd2\Core\Router\CrudController;
use \Nerd2\Core\BrowserBackend;

require_once('vendor/autoload.php');

Nerd::init(function (Nerd $app)
{
    /* Middleware example. This middleware calculates how long request was run and prints result to log. */
    $app->use(function (Context $context, Closure $next) {
        $begin = microtime(true);
        $next();
        $end = microtime(true);
        error_log($end - $begin);
    });

    /* Routes are middleware too. ... */
    $app->use(Route::get('/single', function ($context, $next) {
        $context->response->body = 'Hello from single route!';
    }));

    /* Router is just cascade of routes. ... */
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

    /* Router can have a prefix. ... */
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

    /* Also there are CRUD controller middleware. */
    $app->use(

        new class('/apps') extends CrudController
        {
            public function list(Context $context): void
            {
                $context->response->body = 'List of Apps';
            }

            public function get(Context $context, $id): void
            {
                $context->response->body = "Get App (id=$id)";
            }

            public function post(Context $context): void
            {
                $context->response->body = 'Post new App';
            }

            public function put(Context $context, $id): void
            {
                $context->response->body = 'Update App';
            }

            public function delete(Context $context, $id): void
            {
                $context->response->body = 'Delete App';
            }
        }

    );

});
