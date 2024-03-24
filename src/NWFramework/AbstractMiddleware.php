<?php

declare(strict_types=1);

namespace NW;

abstract class AbstractMiddleware
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    abstract public function __invoke(Request $request, callable $handler): Response;
}
