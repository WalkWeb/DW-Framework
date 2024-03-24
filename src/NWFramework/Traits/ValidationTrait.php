<?php

declare(strict_types=1);

namespace NW\Traits;

use NW\AppException;

trait ValidationTrait
{
    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return string
     * @throws AppException
     */
    protected static function string(array $data, string $filed, string $error): string
    {
        if (!array_key_exists($filed, $data) || !is_string($data[$filed])) {
            throw new AppException($error);
        }

        return $data[$filed];
    }

    /**
     * @param string $string
     * @param int $minLength
     * @param int $maxLength
     * @param string $error
     * @return string
     * @throws AppException
     */
    protected static function stringMinMaxLength(string $string, int $minLength, int $maxLength, string $error): string
    {
        $length = mb_strlen($string);

        if ($length < $minLength || $length > $maxLength) {
            throw new AppException($error);
        }

        return $string;
    }
}
