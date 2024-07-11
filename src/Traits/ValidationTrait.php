<?php

declare(strict_types=1);

namespace WalkWeb\NW\Traits;

use DateTime;
use DateTimeInterface;
use Exception;
use WalkWeb\NW\AppException;
use Ramsey\Uuid\Uuid;

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
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return string|null
     * @throws AppException
     */
    protected static function stringOrNull(array $data, string $filed, string $error): ?string
    {
        if (!array_key_exists($filed, $data)) {
            throw new AppException($error);
        }

        if (is_string($data[$filed]) || $data[$filed] === null) {
            return $data[$filed];
        }

        throw new AppException($error);
    }

    /**
     * @param array $data
     * @param string $filed
     * @param string $default
     * @return string
     */
    protected static function stringOrDefault(array $data, string $filed, string $default): string
    {
        if (!array_key_exists($filed, $data) || !is_string($data[$filed])) {
            return $default;
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

    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return string
     * @throws AppException
     */
    protected static function uuid(array $data, string $filed, string $error): string
    {
        if (!array_key_exists($filed, $data) || !is_string($data[$filed])) {
            throw new AppException($error);
        }

        if (!Uuid::isValid($data[$filed])) {
            throw new AppException($error);
        }

        return $data[$filed];
    }

    /**
     * @param string $string
     * @param string $regexp
     * @param string $error
     * @return string
     * @throws AppException
     */
    protected static function parent(string $string, string $regexp, string $error): string
    {
        if (preg_match($regexp, $string)) {
            return $string;
        }

        throw new AppException($error);
    }

    /**
     * @param string $email
     * @param string $error
     * @return string
     * @throws AppException
     */
    protected static function email(string $email, string $error): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        throw new AppException($error);
    }

    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return bool
     * @throws AppException
     */
    protected static function bool(array $data, string $filed, string $error): bool
    {
        if (!array_key_exists($filed, $data) || !is_bool($data[$filed])) {
            throw new AppException($error);
        }

        return $data[$filed];
    }

    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return DateTimeInterface
     * @throws AppException
     */
    protected static function date(array $data, string $filed, string $error): DateTimeInterface
    {
        if (!array_key_exists($filed, $data) || !is_string($data[$filed])) {
            throw new AppException($error);
        }

        try {
            return new DateTime($data[$filed]);
        } catch (Exception $e) {
            throw new AppException($error);
        }
    }

    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return int
     * @throws AppException
     */
    protected static function int(array $data, string $filed, string $error): int
    {
        if (!array_key_exists($filed, $data) || !is_int($data[$filed])) {
            throw new AppException($error);
        }

        return $data[$filed];
    }

    /**
     * @param int $value
     * @param int $min
     * @param int $max
     * @param string $error
     * @return int
     * @throws AppException
     */
    protected static function intMinMaxValue(int $value, int $min, int $max, string $error): int
    {
        if ($value < $min || $value > $max) {
            throw new AppException($error);
        }

        return $value;
    }

    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return int|float
     * @throws AppException
     */
    protected static function intOrFloat(array $data, string $filed, string $error)
    {
        if (!array_key_exists($filed, $data) || (!is_float($data[$filed]) && !is_int($data[$filed]))) {
            throw new AppException($error);
        }

        return $data[$filed];
    }

    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return array
     * @throws AppException
     */
    protected static function array(array $data, string $filed, string $error): array
    {
        if (!array_key_exists($filed, $data) || !is_array($data[$filed])) {
            throw new AppException($error);
        }

        return $data[$filed];
    }
}
