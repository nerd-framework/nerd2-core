<?php

namespace Nerd2\Core;

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

        $this->services = [];
        $this->state = [];
    }
}
