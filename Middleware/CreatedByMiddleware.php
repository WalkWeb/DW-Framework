<?php

declare(strict_types=1);

namespace Middleware;

use NW\AbstractMiddleware;
use NW\Request;
use NW\Response;

class CreatedByMiddleware extends AbstractMiddleware
{
    public function __invoke(Request $request, callable $handler): Response
    {
        return $handler($request)
            ->withHeader('CreatedBy', 'WalkWeb');
    }
}
