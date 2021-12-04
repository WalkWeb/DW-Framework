<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Cookie;
use Tests\AbstractTestCase;

/**
 * TODO Тест на установку куков написать сейчас невозможно - это приводит к ошибке:
 * TODO Cannot modify header information - headers already sent by (output started at /var/www/dw-framework.loc/vendor/phpunit/phpunit/src/Util/Printer.php:109)
 * TODO Когда работа с куками будет переписана на использование Request/Response - ошибка исчезнет
 *
 * @package Tests\src\NWFramework
 */
class CookieTest extends AbstractTestCase
{
    /**
     * Тест на получение несуществующего кука
     */
    public function testCookieGetNull(): void
    {
        self::assertNull(Cookie::getCookie('no_cookie'));
    }

    /**
     * Тест на проверку несуществующего кука
     */
    public function testCookieCheckFalse(): void
    {
        self::assertFalse(Cookie::checkCookie('no_cookie'));
    }
}
