<?php

namespace Nerd2\Core;

use \Nerd2\Core\Exceptions\HttpException;

class Context
{
    use \Nerd2\Core\Utils\AutoGetterSetter;

    protected static $_autoGetters = ['request', 'response', 'app', 'services', 'state'];

    private $request;
    private $response;
    private $app;
    private $services;
    private $state;

    public function __construct(Request $request, Nerd $app)
    {
        $this->request = $request;
        $this->response = new Response($this);
        $this->app = $app;

        $this->services = (object) [];
        $this->state = (object) [];
    }

    public function throw(int $responseCode, $body = ''): void
    {
        throw new HttpException($responseCode, $body);
    }
}
