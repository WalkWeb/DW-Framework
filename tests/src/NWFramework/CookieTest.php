<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Cookie;
use Tests\AbstractTestCase;

class CookieTest extends AbstractTestCase
{
    public function testCookieSetUpdateDelete(): void
    {
        $key = 'key';
        $value = 'value';

        $cookie = new Cookie();

        // add
        $cookie->setCookie($key, $value);

        self::assertTrue($cookie->checkCookie($key));
        self::assertEquals($value, $cookie->getCookie($key));
        self::assertEquals([$key => $value], $cookie->getCookies());

        // update
        $newValue = 'new_value';

        $cookie->setCookie($key, $newValue);
        self::assertEquals($newValue, $cookie->getCookie($key));
        self::assertEquals([$key => $newValue], $cookie->getCookies());

        // delete
        $cookie->deleteCookie($key);
        self::assertFalse($cookie->checkCookie($key));
        self::assertNull($cookie->getCookie($key));
        self::assertEquals([], $cookie->getCookies());
    }

    /**
     * Тест на получение несуществующего кука
     */
    public function testCookieGetNull(): void
    {
        $cookie = new Cookie();

        self::assertNull($cookie->getCookie('no_cookie'));
    }

    /**
     * Тест на проверку несуществующего кука
     */
    public function testCookieCheckFalse(): void
    {
        $cookie = new Cookie();

        self::assertFalse($cookie->checkCookie('no_cookie'));
    }
}
