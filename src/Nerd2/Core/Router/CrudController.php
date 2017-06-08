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

    public function before(Context $context): void
    {
        //
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
                $this->before($context);
                $this->list($context);
            })
            ->get('/:id', function (Context $context) {
                $this->before($context);
                $this->get($context, $context->getRequest()->getParam('id'));
            })
            ->post('/', function (Context $context) {
                $this->before($context);
                $this->post($context);
            })
            ->put('/:id', function (Context $context) {
                $this->before($context);
                $this->put($context, $context->getRequest()->getParam('id'));
            })
            ->delete('/:id', function (Context $context) {
                $this->before($context);
                $this->delete($context, $context->getRequest()->getParam('id'));
            });

        $router($context, $next);
    }
}
