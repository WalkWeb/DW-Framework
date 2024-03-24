<?php

declare(strict_types=1);

namespace Tests\utils;

use NW\Request;
use NW\Response;

/**
 * Некорректный middleware - не наследуется от AbstractMiddleware
 */
class InvalidMiddleware
{
    public function __invoke(Request $request, callable $handler): Response
    {
        return $handler($request)
            ->withHeader('CreatedBy', 'WalkWeb');
    }
}
