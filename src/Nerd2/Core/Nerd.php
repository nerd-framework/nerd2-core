<?php

namespace Nerd2\Core;

use \Closure;
use \Exception;
use \Nerd2\Core\Exceptions\NerdException;
use \Nerd2\Core\Exceptions\HttpException;

class Nerd
{
    private $middleware = [];

    public static function init(Closure $init): void
    {
        $request = Request::capture();
        $backend = BrowserBackend::getInstance();

        $app = new self();

        $init($app);

        $app->handle($request, $backend);
    }

    public function use(callable $middleware): void
    {
        $this->middleware[] = $middleware;
    }

    public function handle(Request $request, Backend $backend): void
    {
        $context = new Context($request, $this);

        try {
            $this->runMiddleware($context);
        } catch (HttpException $e) {
            $context->response->responseCode = $e->responseCode;
            $context->response->body = $e->body;
        } catch (Exception | NerdException $e) {
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

        $this->runSilently($cascade, $context, $defaultMiddleware);
    }

    private function runSilently(Closure $function, ...$args): void
    {
        ob_start();
        $function(...$args);
        $side = ob_get_clean();

        if (strlen($side) > 0) {
            throw new NerdException("Side-effect body output detected");
        }
    }

    private function sendToClient(Context $context, Backend $backend): void
    {
        $context->response->sendTo($backend);
    }
}
