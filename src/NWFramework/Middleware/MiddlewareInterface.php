<?php

namespace NW\Middleware;

use NW\Request\Request;

interface MiddlewareInterface
{
    public function __invoke(Request $request): void;
}
