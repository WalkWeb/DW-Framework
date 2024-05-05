<?php

declare(strict_types=1);

namespace Tests\src;

use Exception;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Captcha;
use WalkWeb\NW\Container;
use Tests\AbstractTest;

class CaptchaTest extends AbstractTest
{
    /**
     * В проверке генерации картинки мы допускаем, что нам достаточно того, что мы получили строку и никаких ошибок не
     * произошло
     *
     * @throws Exception
     */
    public function testCaptchaGetCaptchaImage(): void
    {
        $capthca = new Captcha($this->getContainer());

        self::assertIsString($capthca->getCaptchaImage());
        self::assertEquals(4, mb_strlen($capthca->getCaptcha()));
    }

    /**
     * Тесты на успешную и неуспешную проверку капчи в DEV/PROD-режиме
     *
     * @dataProvider normalAppEnvDataProvider
     * @param string $appENV
     * @throws AppException
     */
    public function testCaptchaCheckCaptchaNormalMode(string $appENV): void
    {
        $capthca = new Captcha($this->getContainer($appENV));

        $capthca->getCaptchaImage();

        self::assertTrue($capthca->checkCaptcha($capthca->getCaptcha()));
        self::assertFalse($capthca->checkCaptcha('invalid_captcha'));
    }

    /**
     * Тесты на проверку капчи в TEST-режиме - всегда будет true
     *
     * @throws AppException
     */
    public function testCaptchaCheckCaptchaTestMode(): void
    {
        $capthca = new Captcha($this->getContainer());

        $capthca->getCaptchaImage();

        self::assertTrue($capthca->checkCaptcha('1234'));
    }

    public function normalAppEnvDataProvider(): array
    {
        return [
            [
                Container::APP_DEV,
            ],
            [
                Container::APP_PROD,
            ],
        ];
    }
}
