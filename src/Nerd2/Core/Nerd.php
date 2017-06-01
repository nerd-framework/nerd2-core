<?php

namespace Nerd2\Core;

use \Closure;

class Nerd
{
    private $middleware = [];

    public function use(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function handle(Request $request, Backend $backend): void
    {
        $context = new Context($request, $this);

        $this->runMiddleware($context);
        $this->sendToClient($context, $backend);
    }

    private function runMiddleware(Context $context): void
    {
        $defaultMiddleware = function () use ($context) {
            $context->response->responseCode = 404;
            $context->response->body = 'Not found';
        };

        $middleware = $this->middleware;

        $cascade = makeCascade($middleware);
        $cascade($context, $defaultMiddleware);
    }

    private function sendToClient(Context $context, Backend $backend): void
    {
        $context->response->sendTo($backend);
    }
}
