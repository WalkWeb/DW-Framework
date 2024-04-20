<?php

declare(strict_types=1);

namespace Handlers\Cookie;

use NW\AbstractHandler;
use NW\AppException;
use NW\Request;
use NW\Response;
use NW\Traits\ValidationTrait;

class CookieDeleteHandler extends AbstractHandler
{
    use ValidationTrait;

    /**
     * Удаляет указанные куки
     *
     * @param Request $request
     * @return Response
     * @throws AppException
     */
    public function __invoke(Request $request): Response
    {
        try {
            $data = $this->validateData($request->getBody());

            $this->container->getCookies()->delete((string)$data['name']);

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

        return $data;
    }
}
