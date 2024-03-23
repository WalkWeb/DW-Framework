<?php

declare(strict_types=1);

namespace Tests\utils;

use NW\Request;
use NW\Response;

/**
 * Некорректный middleware - не реализован метод __invoke()
 */
class InvalidMiddleware
{
    public function handle(Request $request, callable $handler): Response
    {
        return $handler($request)
            ->withHeader('CreatedBy', 'WalkWeb');
    }
}
