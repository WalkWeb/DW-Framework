<?php

namespace NW;

/**
 * Class Validator
 *
 * Реализовано:
 * (+) email
 * (+) integer
 * (+) string
 * (+) required
 * (+) boolean
 * (+) in - строгое соответствие одному из указанных в массиве величин
 * (+) parent - проверка по регулярному выражению
 *
 * В планах:
 * unique - уникальный элемент в такой-то таблице и такой-то колонке
 *
 *
 * В возможную перспективу:
 * captcha - генерацию и проверку капчи буду делать отдельным классом
 * compare
 * date
 * datetime
 * time
 * double
 * exist
 * file
 * filter
 * image
 * match
 * safe
 * trim
 * url
 * ip
 *
 */
class Validator
{
    /**
     * Имя проверяемого поля
     * @var
     */
    private static $name;

    /**
     * Массив ошибок
     * @var
     */
    private static $errors;

    /**
     * БД
     * @var object dw\core\Connection;
     */
    private static $db;

    /**
     * Принимает валидируемый параметр и дополнительные параметры, и проверяет его на основе правил
     *
     * @param $name - имя поля, необходимо для корректного сообщения об ошибке
     * @param $param - валидируемая переменная
     * @param $rules - правила валидации
     * @param null $table - только для проверки типа unique - таблица для проверки
     * @param null $column - только для проверки типа unique - колонка для проверки
     * @return bool
     */
    public static function check($name, $param, $rules, $table = null, $column = null): bool
    {
        self::$name = $name;
        self::$errors = null;

        // В текущем варианте перебора параметров возвращается ошибка при первом же несоблюдении какого-либо правила
        // Можно доработать, чтобы проверялись все правила, и возвращался сразу список несоответствий
        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                if ($value === 'unique') {
                    if (!self::unique($param, $table, $column)) {
                        return false;
                    }
                } elseif (!self::$value($param)) {
                    return false;
                }
            } elseif (!self::$key($param, $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Возвращает ошибку
     *
     * @return string
     */
    public static function getError(): string
    {
        $message = '';

        if (self::$errors) {
            foreach (self::$errors as $error) {
                $message .= $error;
            }
        }

        return $message;
    }

    /**
     * Проверяет значение на строку
     *
     * @param $param
     * @return bool
     */
    protected static function string($param): bool
    {
        if (is_string($param)) {
            return true;
        }
        self::$errors[] = self::$name . ' должен быть строкой';
        return false;
    }

    /**
     * Проверяет значение на целое число
     *
     * @param $param
     * @return bool
     */
    protected static function int($param): bool
    {
        if (is_int($param)) {
            return true;
        }
        self::$errors[] = self::$name . ' должен быть числом';
        return false;
    }

    /**
     * Проверяет строку на минимальную длину
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected static function min($param, $value): bool
    {
        if (strlen($param) >= $value) {
            return true;
        }
        self::$errors[] = self::$name . ' должен быть больше или равен ' . $value . ' символов';
        return false;
    }

    /**
     * Проверяет строку на максимальную длину
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected static function max($param, $value): bool
    {
        if (mb_strlen($param) <= $value) {
            return true;
        }
        self::$errors[] = self::$name . ' должен быть меньше или равен ' . $value . ' символов';
        return false;
    }

    /**
     * Проверяет корректность указанной почты
     *
     * @param $param
     * @return bool
     */
    protected static function mail($param): bool
    {
        if (filter_var($param, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        self::$errors[] = 'указана некорректная почта';
        return false;
    }

    /**
     * Проверяет на пустоту
     *
     * @param $param
     * @return bool
     */
    protected static function required($param): bool
    {
        if ($param === null || $param === '') {
            self::$errors[] = self::$name . ' не может быть пустым';
            return false;
        }
        return true;
    }

    /**
     * Проверяет на логический тип значения
     *
     * @param $param
     * @return bool
     */
    protected static function boolean($param): bool
    {
        if (is_bool($param)) {
            return true;
        }
        self::$errors[] = self::$name . ' должен быть логическим типом';
        return false;
    }

    /**
     * Проверяет на строгое соответствие одному из указанных в массиве величин
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected static function in($param, $value): bool
    {
        foreach ($value as $val) {
            if ($param === $val) {
                return true;
            }
        }

        self::$errors[] = self::$name . ' указан некорректно';
        return false;
    }

    /**
     * Проверка по регулярному выражению
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected static function parent($param, $value): bool
    {
        if (preg_match($value, $param)) {
            return true;
        }
        self::$errors[] = self::$name . ' указан некорректно';
        return false;
    }

    /**
     * Проверка на уникальное значение в таблице
     *
     * @param $param
     * @param $table
     * @param $column
     * @return bool
     */
    private static function unique($param, $table, $column): bool
    {
        if ($table === null || $column === null) {
            die('Неуказана таблица или колонка для проверки');
        }
        self::connectDB();
        if (!self::$db->query("SELECT $column FROM $table WHERE $column = ?", [['type' => 's', 'value' => $param]])) {
            return true;
        }
        self::$errors[] = 'Указанный ' . self::$name . ' уже существует, выберите другой';
        return false;
    }

    /**
     * Подключение к базе
     */
    private static function connectDB(): void
    {
        if (!self::$db) {
            self::$db = Connection::getInstance();
        }
    }
}
