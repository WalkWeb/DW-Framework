<?php

namespace NW\Middleware;

interface MiddlewareInterface
{
    public function __invoke(): void;
}
