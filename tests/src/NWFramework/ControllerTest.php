<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use Tests\AbstractTestCase;
use Tests\utils\TestAbstractController;

class ControllerTest extends AbstractTestCase
{
    /**
     * @dataProvider jsonDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testControllerJson(array $data): void
    {
        $controller = new TestAbstractController();

        $response = $controller->json($data);

        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals('1.1', $response->getProtocolVersion());
        self::assertEquals('OK', $response->getReasonPhase());
        self::assertEquals(json_encode($data, JSON_THROW_ON_ERROR), $response->getBody());
        self::assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());
    }

    /**
     * @throws Exception
     */
    public function testControllerErrorPage(): void
    {
        $controller = new TestAbstractController();

        $response = $controller->renderErrorPage();

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('1.1', $response->getProtocolVersion());
        self::assertEquals('Not Found', $response->getReasonPhase());
        self::assertEquals([], $response->getHeaders());

        // Из-за вывода статистики runtime невозможно точно предугадать, какой будет body
        // По этому пока просто проверяем, что получили строку
        self::assertIsString($response->getBody());
    }

    public function testControllerCheckCache(): void
    {
        $cacheName = 'name';
        $cacheTime = 100;
        $cacheId = 'uuid';
        $cacheContent = 'content';
        $cachePrefix = '_cache';
        $controller = new TestAbstractController();

        // Если тест запускается не первый раз - то кэш уже будет, соответственно его нужно удалить
        $cacheFile = __DIR__ . '/../../../cache/html/' . $cacheName . '_' . $cacheId;

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        // Вначале кэш отсутствует
        self::assertFalse($controller->checkCache($cacheName, $cacheTime, $cacheId));

        // Создаем кэш
        $controller->createCache($cacheName, $cacheContent, $cacheId, $cachePrefix);

        self::assertEquals($cacheContent . $cachePrefix, $controller->checkCache($cacheName, $cacheTime, $cacheId));
    }

    /**
     * @return array
     */
    public function jsonDataProvider(): array
    {
        return [
            [
                [
                    'content' => 'test json content',
                ],
            ],
            [
                [
                    'version' => '1.0',
                    'content' => ['param1', 'param2', 'param3'],
                ],
            ],
        ];
    }
}
