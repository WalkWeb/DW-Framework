<?php

declare(strict_types=1);

namespace WalkWeb\NW;

abstract class AbstractMiddleware
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    abstract public function __invoke(Request $request, callable $handler): Response;

    /**
     * @param string $uri
     * @return Response
     * @throws AppException
     */
    protected function redirect(string $uri): Response
    {
        $response = new Response('', Response::FOUND);
        $response->withHeader('Location', $uri);
        return $response;
    }
}
