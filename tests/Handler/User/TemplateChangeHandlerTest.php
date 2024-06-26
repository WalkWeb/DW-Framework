<?php

declare(strict_types=1);

namespace Tests\Handler\User;

use Domain\User\UserInterface;
use WalkWeb\NW\App;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Request;
use WalkWeb\NW\Response;
use Tests\AbstractTest;

class TemplateChangeHandlerTest extends AbstractTest
{
    /**
     * Тест на ситуацию, когда неавторизованный пользователь обращается к методу на смену шаблона сайта
     *
     * @throws AppException
     */
    public function testTemplateChangeHandlerNoAuth(): void
    {
        $request = new Request(
            ['REQUEST_URI' => '/change_template/light'],
        );
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertEquals('{"success":false,"error":"You are not authorized"}', $response->getBody());
    }

    /**
     * Тест на ситуацию, когда меняется шаблон на аналогичный существующему - просто ничего не делается
     *
     * @throws AppException
     */
    public function testTemplateChangeHandlerNoChange(): void
    {
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFGyyyy';
        $request = new Request(['REQUEST_URI' => '/change_template/default'], [], [UserInterface::AUTH_TOKEN => $authToken]);

        self::assertJsonSuccess($this->app->handle($request));
    }

    /**
     * Тест на ситуацию, когда указан неизвестный шаблон
     *
     * @throws AppException
     */
    public function testTemplateChangeHandlerUnknownTemplate(): void
    {
        $template = 'xxx';
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFGyyyy';
        $request = new Request(['REQUEST_URI' => "/change_template/$template"], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $response = $this->app->handle($request);

        self::assertEquals(Response::OK, $response->getStatusCode());
        self::assertEquals('{"success":false,"error":"Unknown template: ' . $template . '"}', $response->getBody());
    }

    /**
     * Тест на успешную смену шаблона
     *
     * @throws AppException
     */
    public function testTemplateChangeHandlerSuccessChange(): void
    {
        $template = 'light';
        $container = $this->getContainer();
        $container->getConnectionPool()->getConnection()->autocommit(false);
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFGyyyy';
        $request = new Request(['REQUEST_URI' => "/change_template/$template"], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $router = require __DIR__ . '/../../../routes/web.php';
        $app = new App($router, $container);

        self::assertJsonSuccess($app->handle($request));
        self::assertEquals($template, $container->getUser()->getTemplate());

        $container->getConnectionPool()->getConnection()->rollback();
    }
}
