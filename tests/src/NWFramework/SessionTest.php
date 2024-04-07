<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Session;
use Tests\AbstractTest;

class SessionTest extends AbstractTest
{
    public function testSessionStart(): void
    {
        Session::start();

        self::assertTrue(Session::existSession());
    }

    public function testSessionEnd(): void
    {
        Session::end();

        // Достаточно того, что ничего не упало
        self::assertTrue(true);
    }

    public function testSessionSetAndGetParam(): void
    {
        $key = 'hello';
        $parameter = 'world';

        Session::setParam($key, $parameter);

        self::assertEquals($parameter, Session::getParam($key));
    }

    public function testSessionNullParam(): void
    {
        self::assertNull(Session::getParam('missed_parameter'));
    }

    public function testSessionCheckParam(): void
    {
        $key = 'hello';
        $parameter = 'world';

        Session::setParam($key, $parameter);

        self::assertTrue(Session::existParam($key));
        self::assertFalse(Session::existParam('undefined_parameter'));
    }
}
