<?php

declare(strict_types=1);

namespace Tests\handlers;

use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class RedirectHandlerTest extends AbstractTestCase
{
    /**
     * Тест на редирект
     *
     * @throws AppException
     */
    public function testRedirectPage(): void
    {
        $request = new Request(['REQUEST_URI' => '/redirect']);
        $response = $this->app->handle($request);

        self::assertEquals(['Location' => 'https://www.google.com/'], $response->getHeaders());
        self::assertEquals(Response::FOUND, $response->getStatusCode());
    }
}
