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

        try {
            $this->runMiddleware($context);
        } catch (\Exception $e) {
            $context->response->responseCode = 500;
            error_log($e);
        }

        $this->sendToClient($context, $backend);
    }

    private function runMiddleware(Context $context): void
    {
        $defaultMiddleware = function () use ($context) {
            $context->response->responseCode = 404;
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
