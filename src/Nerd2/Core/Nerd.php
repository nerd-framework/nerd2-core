<?php

namespace Nerd2\Core;

use \Closure;

class Nerd
{
    private $middleware = [];

    public function use(Closure $middleware)
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

        $cascade = array_reduce(array_reverse($this->middleware), function ($next, $prev) use ($context) {
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
