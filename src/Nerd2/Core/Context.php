<?php

namespace Nerd2\Core;

class Context
{
    public $request;
    public $response;
    public $services = [];
    public $state = [];

    public function __construct(Request $request) {
        $this->request = $request;
        $this->response = Response::empty();
    }
}
