<?php

namespace Nerd2\Core;

class ApplicationException extends \Exception
{
    public $status;
    public $body;

    public function __construct(int $status, $body = null)
    {
        parent::__construct();
        $this->status = $status;
        $this->body = $body;
    }
}
