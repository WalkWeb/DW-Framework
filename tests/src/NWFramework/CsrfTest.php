<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Csrf;
use NW\Session;
use Tests\AbstractTest;

class CsrfTest extends AbstractTest
{
    /**
     * Тест на генерацию csrf-токена
     */
    public function testCsrfGetCsrfToken(): void
    {
        $token = Csrf::getCsrfToken();

        self::assertIsString($token);
        self::assertEquals(15, mb_strlen($token));

        self::assertTrue(Session::existParam(Csrf::TOKEN_NAME));
    }

    /**
     * Тест на успешную и неуспешную проверку csrf-токена
     */
    public function testCsrfCheckCsrfToken(): void
    {
        self::assertFalse(Csrf::checkCsrfToken('no_generated_token'));

        $token = Csrf::getCsrfToken();

        self::assertFalse(Csrf::checkCsrfToken('invalid_token'));
        self::assertTrue(Csrf::checkCsrfToken($token));
    }
}
