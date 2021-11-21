<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Captcha;
use Tests\AbstractTestCase;

class CaptchaTest extends AbstractTestCase
{
    /**
     * В проверке генерации картинки мы допускаем, что нам достаточно того, что мы получили строку и никаких ошибок не
     * произошло
     */
    public function testCaptchaGetCaptchaImage(): void
    {
        self::assertIsString(Captcha::getCaptchaImage());
        self::assertEquals(4, mb_strlen(Captcha::getCaptcha()));
    }

    /**
     * Тесты на успешную и неуспешную проверку капчи
     */
    public function testCaptchaCheckCaptcha(): void
    {
        Captcha::getCaptchaImage();

        self::assertTrue(Captcha::checkCaptcha(Captcha::getCaptcha()));
        self::assertFalse(Captcha::checkCaptcha('invalid_captcha'));
    }
}
