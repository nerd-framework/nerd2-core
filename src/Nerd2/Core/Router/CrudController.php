<?php

namespace Nerd2\Core\Router;

use \Nerd2\Core\Context;
use \Closure;

abstract class CrudController
{
    private $routePrefix;

    public function __construct(string $routePrefix)
    {
        $this->routePrefix = $routePrefix;
    }

    public function list(Context $context): void
    {
        $context->throw(501);
    }

    public function get(Context $context, $id): void
    {
        $context->throw(501);
    }

    public function post(Context $context): void
    {
        $context->throw(501);
    }

    public function put(Context $context, $id): void
    {
        $context->throw(501);
    }

    public function delete(Context $context, $id): void
    {
        $context->throw(501);
    }

    public function __invoke(Context $context, Closure $next): void
    {
        $router = (new Router($this->routePrefix))
            ->get('/', function (Context $context) {
                $this->list($context);
            })
            ->get('/:id', function (Context $context) {
                $this->get($context, $context->request->params['id']);
            })
            ->post('/', function (Context $context) {
                $this->post($context);
            })
            ->put('/:id', function (Context $context) {
                $this->put($context, $context->request->params['id']);
            })
            ->delete('/:id', function (Context $context) {
                $this->delete($context, $context->request->params['id']);
            });

        $router($context, $next);
    }
}
