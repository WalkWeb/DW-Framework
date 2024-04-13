<?php

declare(strict_types=1);

namespace Handlers\Cookie;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Traits\ValidationTrait;

class CookieAddHandler extends AbstractHandler
{
    use ValidationTrait;

    /**
     * Добавляет новые куки
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $data = $this->validateData($request->getBody());

            $this->container->getCookies()->set($data['name'], $data['value']);

            return $this->redirect('/cookies');
        } catch (AppException $e) {
            return $this->render('/cookies/index', [
                'cookies' => $this->container->getRequest()->getCookies()->getArray(),
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param array $data
     * @return array
     * @throws AppException
     */
    private function validateData(array $data): array
    {
        self::string($data, 'name', 'Bad request: "name" value required and expected string');

        self::stringMinMaxLength(
            $data['name'],
            1,
            50,
            'Length "name" parameter must be from 1 to 50 characters',
        );

        self::string($data, 'value', 'Bad request: "value" value required and expected string');

        self::stringMinMaxLength(
            $data['value'],
            1,
            100,
            'Length "value" parameter must be from 1 to 100 characters',
        );

        return $data;
    }
}
