<?php

namespace NW;

/**
 * Реализовано:
 * (+) email
 * (+) integer
 * (+) string
 * (+) required
 * (+) boolean
 * (+) in - строгое соответствие одному из указанных в массиве величин
 * (+) parent - проверка по регулярному выражению
 * (+) unique - уникальный элемент в такой-то таблице и такой-то колонке
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
     *
     * @var string
     */
    private static $name;

    /**
     * Массив ошибок
     *
     * @var array
     */
    private static $errors;

    /**
     * БД
     *
     * @var Connection;
     */
    private static $db;

    /**
     * @var string|null
     */
    private static $defaultError;

    /**
     * Принимает валидируемый параметр и дополнительные параметры, и проверяет его на основе правил
     *
     * @param string $name        - Имя поля, необходимо для корректного сообщения об ошибке
     * @param $param              - Валидируемая переменная
     * @param array $rules        - Правила валидации
     * @param string|null $table  - Только для проверки типа unique - таблица для проверки
     * @param string|null $column - Только для проверки типа unique - колонка для проверки
     * @param string|null $error  - Текст ошибки, если он не указан - то текст ошибки будет составлен автоматически
     * @return bool
     * @throws AppException
     *@uses string, int, min, max, required, boolean, in, parent, unique
     */
    public static function check(string $name, $param, array $rules, string $table = null, string $column = null, string $error = null): bool
    {
        self::$name = $name;
        self::$errors = null;
        self::$defaultError = $error;

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
        self::addError(self::$name . ' должен быть строкой');
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
        self::addError(self::$name . ' должен быть числом');
        return false;
    }

    /**
     * Проверяет строку на минимальную длину
     *
     * TODO Доработать и проверку к int
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected static function min($param, $value): bool
    {
        if (mb_strlen($param) >= $value) {
            return true;
        }
        self::addError(self::$name . ' должен быть больше или равен ' . $value . ' символов');
        return false;
    }

    /**
     * Проверяет строку на максимальную длину
     *
     * TODO Доработать и проверку к int
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
        self::addError(self::$name . ' должен быть меньше или равен ' . $value . ' символов');
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
        self::addError('Указана некорректная почта');
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
            self::addError(self::$name . ' не может быть пустым');
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
        self::addError(self::$name . ' должен быть логическим типом');
        return false;
    }

    /**
     * Проверяет на строгое соответствие одному из указанных в массиве величин
     *
     * @param $param
     * @param array $values
     * @return bool
     */
    protected static function in($param, array $values): bool
    {
        if (in_array($param, $values, true)) {
            return true;
        }

        self::addError(self::$name . ' указан некорректно');

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
        self::addError(self::$name . ' указан некорректно');
        return false;
    }

    /**
     * Проверка на уникальное значение в таблице
     *
     * @param $param
     * @param $table
     * @param $column
     * @return bool
     * @throws AppException
     */
    private static function unique($param, $table, $column): bool
    {
        if ($table === null || $column === null) {
            self::addError('Неуказана таблица или колонка для проверки');
            return false;
        }

        self::connectDB();

        if (!self::$db->query("SELECT $column FROM $table WHERE $column = ?", [['type' => 's', 'value' => $param]])) {
            return true;
        }

        self::addError('Указанный ' . self::$name . ' уже существует, выберите другой');

        return false;
    }

    /**
     * Добавляет ошибку валидации
     *
     * @param string $error
     */
    private static function addError(string $error): void
    {
        if (self::$defaultError) {
            self::$errors[] = self::$defaultError;
        } else {
            self::$errors[] = $error;
        }
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
