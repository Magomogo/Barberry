<?php

namespace Barberry\Controller;

use Symfony\Component\HttpFoundation;

interface ControllerInterface
{
    public function get(): HttpFoundation\Response;

    public function post(): HttpFoundation\Response;

    public function delete(): HttpFoundation\Response;
}
