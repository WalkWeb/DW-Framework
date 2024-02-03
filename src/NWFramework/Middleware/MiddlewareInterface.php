<?php

namespace NW\Middleware;

use NW\Request;

interface MiddlewareInterface
{
    public function __invoke(Request $request): void;
}
