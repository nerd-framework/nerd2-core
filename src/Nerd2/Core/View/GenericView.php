<?php

namespace Nerd2\Core\View;

use Nerd2\Core\Nerd;
use Nerd2\Service\ServiceProvider;

class GenericView implements ServiceProvider
{
    private $viewsPath;
    private $suffix;

    public function __construct(string $viewsPath, string $suffix = '')
    {
        $this->viewsPath = $viewsPath;
        $this->suffix = $suffix;
    }

    private function render($__viewFile, array $params)
    {
        if (!file_exists($__viewFile)) {
            throw new \Exception("Template ${__viewFile} does not exist");
        }

        extract($params);
        ob_start();
        require $__viewFile;
        return ob_get_clean();
    }

    private function getFullTemplatePath(string $template): string
    {
        return $this->viewsPath . DIRECTORY_SEPARATOR . $template . $this->suffix;
    }

    public function register(Nerd $app): void
    {
        $app->registerService('render', function (string $template, array $params = []): string {
            return $this->render($this->getFullTemplatePath($template), $params);
        });
    }
}
