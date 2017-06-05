<?php

namespace Nerd2\Core\Middleware;

use \Nerd2\Core\Context;
use \Closure;

class PhpViewEngine
{
    private $suffix;
    private $viewPath;

    public function __construct(string $viewPath, string $suffix = '')
    {
        $this->viewPath = $viewPath;
        $this->suffix = $suffix;
    }

    public function __invoke(Context $context, Closure $next): void
    {
        $context->services->render = function (string $template, array $args) {
            $fullTemplatePath = $this->viewPath . DIRECTORY_SEPARATOR . $template . $this->suffix;
            return $this->renderToString($fullTemplatePath, $args);
        };
        $next();
    }

    private function renderToString($file, array $args): string
    {
        ob_start();
        extract($args);
        $result = require($file);
        return ob_get_clean();
    }
}
