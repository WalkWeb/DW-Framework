<?php

declare(strict_types=1);

namespace Middleware;

use WalkWeb\NW\AbstractMiddleware;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;

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
                $this->container->getRuntime()->getStatistic() . ', queries: ' . $this->container->getConnectionPool()->getConnection()->getCountQuery()
            );
    }
}
