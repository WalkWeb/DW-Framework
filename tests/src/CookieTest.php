<?php

declare(strict_types=1);

namespace Tests\src;

use WalkWeb\NW\Cookie;
use Tests\AbstractTest;

class CookieTest extends AbstractTest
{
    public function testCookieSetUpdateDelete(): void
    {
        $key = 'key';
        $value = 'value';

        $cookie = new Cookie();

        // add
        $cookie->set($key, $value);

        self::assertTrue($cookie->check($key));
        self::assertEquals($value, $cookie->get($key));
        self::assertEquals([$key => $value], $cookie->getArray());

        // update
        $newValue = 'new_value';

        $cookie->set($key, $newValue);
        self::assertEquals($newValue, $cookie->get($key));
        self::assertEquals([$key => $newValue], $cookie->getArray());

        // delete
        $cookie->delete($key);
        self::assertFalse($cookie->check($key));
        self::assertNull($cookie->get($key));
        self::assertEquals([], $cookie->getArray());
    }

    /**
     * Тест на получение несуществующего кука
     */
    public function testCookieGetNull(): void
    {
        $cookie = new Cookie();

        self::assertNull($cookie->get('no_cookie'));
    }

    /**
     * Тест на проверку несуществующего кука
     */
    public function testCookieCheckFalse(): void
    {
        $cookie = new Cookie();

        self::assertFalse($cookie->check('no_cookie'));
    }
}
