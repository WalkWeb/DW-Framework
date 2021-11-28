<?php

declare(strict_types=1);

namespace Tests\src\NWFramework;

use Exception;
use Tests\AbstractTestCase;
use Tests\utils\TestController;

class ControllerTest extends AbstractTestCase
{
    /**
     * @dataProvider jsonDataProvider
     * @param array $data
     * @throws Exception
     */
    public function testControllerJson(array $data): void
    {
        $controller = new TestController();

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
        $controller = new TestController();

        $response = $controller->renderErrorPage();

        self::assertEquals(404, $response->getStatusCode());
        self::assertEquals('1.1', $response->getProtocolVersion());
        self::assertEquals('Not Found', $response->getReasonPhase());
        self::assertEquals([], $response->getHeaders());

        // Из-за вывода статистики runtime невозможно точно предугадать, какой будет body
        // По этому пока просто проверяем, что получили строку
        self::assertIsString($response->getBody());
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
