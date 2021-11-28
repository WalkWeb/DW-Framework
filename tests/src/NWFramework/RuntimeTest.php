<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use NW\Runtime;
use Tests\AbstractTestCase;

class RuntimeTest extends AbstractTestCase
{
    public function testRuntime(): void
    {
        Runtime::start();

        // Из-за разного контекста вызова теста (отдельно или в общем запуске всех тестов) runtime будет сильно различаться
        // По этому просто проверяем, что получен float. Когда Runtime будет переписан и уйдет от статики - будет лучше
        self::assertIsFloat(Runtime::getRuntime());
        // Расход будет отличаться и расход памяти: в районе от 0 до 450000 байт
        self::assertTrue(Runtime::getMemoryCost() < 5000000);
        // Что именно выведет заранее не предсказать, это может быть и "0 b" и, например "60.77 kb"
        self::assertIsString(Runtime::getMemoryCostClipped());
    }
}
