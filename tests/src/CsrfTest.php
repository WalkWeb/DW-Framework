<?php

declare(strict_types=1);

namespace Tests\src;

use WalkWeb\NW\AppException;
use WalkWeb\NW\Container;
use WalkWeb\NW\Csrf;
use WalkWeb\NW\Session;
use Tests\AbstractTest;

class CsrfTest extends AbstractTest
{
    /**
     * Тест на генерацию csrf-токена
     *
     * @throws AppException
     */
    public function testCsrfGetCsrfToken(): void
    {
        $container = $this->getContainer(Container::APP_PROD);
        $csrf = new Csrf($container);
        $token = $csrf->getCsrfToken();

        self::assertIsString($token);
        self::assertEquals(15, mb_strlen($token));

        self::assertTrue(Session::existParam(Csrf::TOKEN_NAME));
    }

    /**
     * Тест на успешную и неуспешную проверку csrf-токена
     *
     * @throws AppException
     */
    public function testCsrfCheckCsrfToken(): void
    {
        $container = $this->getContainer(Container::APP_PROD);
        $csrf = new Csrf($container);

        self::assertFalse($csrf->checkCsrfToken('no_generated_token'));

        $token = $csrf->getCsrfToken();

        self::assertFalse($csrf->checkCsrfToken('invalid_token'));
        self::assertTrue($csrf->checkCsrfToken($token));
    }
}
