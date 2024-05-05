<?php

namespace WalkWeb\NW;

use Exception;
use WalkWeb\NW\Traits\StringTrait;

class Csrf
{
    use StringTrait;

    private Container $container;

    public const TOKEN_NAME = 'csrf';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Создает и возвращает CSRF-токен для формы.
     *
     * Это самый простой вариант защиты от CSRF-атак, когда создается один простой токен на всю длину сессии.
     * В будущем защиту от CSRF нужно будет улучшить.
     *
     * @return string
     * @throws AppException
     */
    public function getCsrfToken(): string
    {
        try {
            $token = Session::getParam(self::TOKEN_NAME);

            if (!isset($token)) {
                $string = self::generateString();
                Session::setParam(self::TOKEN_NAME, $string);
            } else {
                $string = $token;
            }

            return $string;
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * Проверяет CSRF-токен
     *
     * @param string $token
     * @return bool
     */
    public function checkCsrfToken(string $token): bool
    {
        if ($this->container->getAppEnv() === Container::APP_TEST) {
            return true;
        }

        if (!Session::existParam(self::TOKEN_NAME)) {
            return false;
        }

        if (hash_equals(Session::getParam(self::TOKEN_NAME), $token)) {
            return true;
        }

        return false;
    }
}
