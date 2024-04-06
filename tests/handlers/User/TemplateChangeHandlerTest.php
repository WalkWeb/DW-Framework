<?php

declare(strict_types=1);

namespace Tests\handlers\User;

use Models\User\UserInterface;
use NW\App;
use NW\AppException;
use NW\Request;
use NW\Response;
use Tests\AbstractTestCase;

class TemplateChangeHandlerTest extends AbstractTestCase
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
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
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
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
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
        $authToken = 'VBajfT8P6PFtrkHhCqb7ZNwIFG45a5';
        $request = new Request(['REQUEST_URI' => "/change_template/$template"], [], [UserInterface::AUTH_TOKEN => $authToken]);
        $router = require __DIR__ . '/../../../routes/web.php';
        $app = new App($router, $container);

        self::assertJsonSuccess($app->handle($request));
        self::assertEquals($template, $container->getUser()->getTemplate());

        $container->getConnectionPool()->getConnection()->rollback();
    }
}
