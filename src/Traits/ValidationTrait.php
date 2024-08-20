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
     * @param string $field
     * @param string $error
     * @return string
     * @throws AppException
     */
    protected static function string(array $data, string $field, string $error): string
    {
        if (!array_key_exists($field, $data) || !is_string($data[$field])) {
            throw new AppException($error);
        }

        return $data[$field];
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $error
     * @return string|null
     * @throws AppException
     */
    protected static function stringOrNull(array $data, string $field, string $error): ?string
    {
        if (!array_key_exists($field, $data)) {
            throw new AppException($error);
        }

        if (is_string($data[$field]) || $data[$field] === null) {
            return $data[$field];
        }

        throw new AppException($error);
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $default
     * @return string
     */
    protected static function stringOrDefault(array $data, string $field, string $default): string
    {
        if (!array_key_exists($field, $data) || !is_string($data[$field])) {
            return $default;
        }

        return $data[$field];
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
     * @param string $field
     * @param string $error
     * @return string
     * @throws AppException
     */
    protected static function uuid(array $data, string $field, string $error): string
    {
        if (!array_key_exists($field, $data) || !is_string($data[$field])) {
            throw new AppException($error);
        }

        if (!Uuid::isValid($data[$field])) {
            throw new AppException($error);
        }

        return $data[$field];
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $error
     * @return string|null
     * @throws AppException
     */
    protected static function uuidOrNull(array $data, string $field, string $error): ?string
    {
        if (!array_key_exists($field, $data)) {
            throw new AppException($error);
        }

        if ($data[$field] === null) {
            return null;
        }

        if (is_string($data[$field]) && Uuid::isValid($data[$field])) {
            return $data[$field];
        }

        throw new AppException($error);
    }

    /**
     * TODO Стоит подумать над добавлением / вначале и в конце, чтобы не приходилось добавлять это каждый раз вручную
     *
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
     * TODO Подумать над переделкой в $data[$field]
     *
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
     * @param string $field
     * @param string $error
     * @return bool
     * @throws AppException
     */
    protected static function bool(array $data, string $field, string $error): bool
    {
        if (!array_key_exists($field, $data) || !is_bool($data[$field])) {
            throw new AppException($error);
        }

        return $data[$field];
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $error
     * @return DateTimeInterface
     * @throws AppException
     */
    protected static function date(array $data, string $field, string $error): DateTimeInterface
    {
        if (!array_key_exists($field, $data) || !is_string($data[$field])) {
            throw new AppException($error);
        }

        try {
            return new DateTime($data[$field]);
        } catch (Exception $e) {
            throw new AppException($error);
        }
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $error
     * @return int
     * @throws AppException
     */
    protected static function int(array $data, string $field, string $error): int
    {
        if (!array_key_exists($field, $data) || !is_int($data[$field])) {
            throw new AppException($error);
        }

        return $data[$field];
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
     * @param string $field
     * @param string $error
     * @return int|float
     * @throws AppException
     */
    protected static function intOrFloat(array $data, string $field, string $error)
    {
        if (!array_key_exists($field, $data) || (!is_float($data[$field]) && !is_int($data[$field]))) {
            throw new AppException($error);
        }

        return $data[$field];
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $error
     * @return int|null
     * @throws AppException
     */
    protected static function intOrNull(array $data, string $field, string $error): ?int
    {
        if (!array_key_exists($field, $data)) {
            throw new AppException($error);
        }

        if (is_int($data[$field]) || $data[$field] === null) {
            return $data[$field];
        }

        throw new AppException($error);
    }

    /**
     * @param array $data
     * @param string $field
     * @param string $error
     * @return array
     * @throws AppException
     */
    protected static function array(array $data, string $field, string $error): array
    {
        if (!array_key_exists($field, $data) || !is_array($data[$field])) {
            throw new AppException($error);
        }

        return $data[$field];
    }

    /**
     * @param array $data
     * @param string $filed
     * @param string $error
     * @return DateTimeInterface|null
     * @throws AppException
     */
    protected static function dateOrNull(array $data, string $filed, string $error): ?DateTimeInterface
    {
        if (!array_key_exists($filed, $data) || (!is_string($data[$filed]) && !is_null($data[$filed]))) {
            throw new AppException($error);
        }

        if (is_null($data[$filed])) {
            return null;
        }

        try {
            $date = new DateTime($data[$filed]);
        } catch (Exception $e) {
            throw new AppException($error);
        }

        return $date;
    }
}
