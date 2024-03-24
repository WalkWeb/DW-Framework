<?php

declare(strict_types=1);

namespace Middleware;

use NW\AbstractMiddleware;
use NW\AppException;
use NW\Request;
use NW\Response;

class StatisticsMiddleware extends AbstractMiddleware
{
    /**
     * @param Request $request
     * @param callable $handler
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request, callable $handler): Response
    {
        return $handler($request)
            ->withHeader(
                'Statistic',
                $this->container->getRuntime()->getStatistic() . ', queries: ' . $this->container->getConnection()->getQueryNumber()
            );
    }
}
