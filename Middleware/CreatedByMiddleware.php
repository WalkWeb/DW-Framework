<?php

declare(strict_types=1);

namespace Middleware;

use WalkWeb\NW\AbstractMiddleware;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

class CreatedByMiddleware extends AbstractMiddleware
{
    public function __invoke(Request $request, callable $handler): Response
    {
        return $handler($request)
            ->withHeader('CreatedBy', 'WalkWeb');
    }
}
