<?php

namespace Nerd2\Service;

use Nerd2\Core\Nerd;

interface ServiceProvider
{
    public function register(Nerd $app): void;
}
