<?php

namespace Nerd2\Core;

use \Closure;

class Nerd
{
    private $middleware = [];

    public function use(callable $middleware)
    {
        $this->middleware[] = $middleware;
    }

    public function run(Request $request)
    {
        $context = new Context($request);
        $this->runMiddleware($context);
        $this->sendToClient($context);
    }

    private function runMiddleware(Context $context): void
    {
        $initialMiddleware = function () use ($context) {
            $context->response->status = 404;
            $context->response->body = 'Not found';
        };

        $errorMiddleware = function (Context $context, Closure $next): void
        {
            try {
                $next();
            } catch (ApplicationException $e) {
                $context->response->status = $e->status;
                $context->response->body = $e->body;
            } catch (\Exception $e) {
                $context->response->status = 500;
                $context->response->body = (string) $e;
            }
        };

        $middleware = array_merge($this->middleware, [$errorMiddleware]);

        $cascade = array_reduce($middleware, function ($next, $prev) use ($context) {
            return function () use ($next, $prev, $context) {
                $prev($context, $next);
            };
        }, $initialMiddleware);

        $cascade();
    }

    private function sendToClient(Context $context)
    {
        $context->response->send(Client::current());
    }
}
