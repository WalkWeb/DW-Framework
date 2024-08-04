<?php

declare(strict_types=1);

namespace Tests\utils;

use WalkWeb\NW\AbstractMiddleware;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

class ExampleMiddleware extends AbstractMiddleware
{
    /**
     * @param Request $request
     * @param callable $handler
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request, callable $handler): Response
    {
        throw new AppException('ExampleMiddleware Exception');
    }

    /**
     * @param string $uri
     * @return Response
     * @throws AppException
     */
    public function redirect(string $uri): Response
    {
        return parent::redirect($uri);
    }
}
