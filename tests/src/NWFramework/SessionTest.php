<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Session;
use Tests\AbstractTestCase;

class SessionTest extends AbstractTestCase
{
    public function testSessionSetAndGetParam(): void
    {
        $key = 'hello';
        $parameter = 'world';

        Session::setParam($key, $parameter);

        self::assertEquals($parameter, Session::getParam($key));
    }

    public function testSessionNullParam(): void
    {
        // Сейчас при отсутствии параметра возвращается null
        self::assertNull(Session::getParam('missed_parameter'));
    }

    public function testSessionCheckParam(): void
    {
        $key = 'hello';
        $parameter = 'world';

        Session::setParam($key, $parameter);

        self::assertTrue(Session::checkParam($key));
        self::assertFalse(Session::checkParam('undefined_parameter'));
    }
}
