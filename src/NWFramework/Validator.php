<?php

namespace NW;

use NW\MySQL\Connection;

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
    private string $name;

    /**
     * Массив ошибок
     *
     * @var array
     */
    private array $errors;

    /**
     * БД
     *
     * @var Connection;
     */
    private Connection $connection;

    /**
     * @var string|null
     */
    private ?string $defaultError;

    /**
     * TODO Переделать - храним пулл коннектов, при проверке unique запрашиваем название в какой базе смотреть
     *
     * @param Container $container
     * @throws AppException
     */
    public function __construct(Container $container)
    {
        $this->connection = $container->getConnectionPool()->getConnection();
    }

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
     * @uses string, int, min, max, required, boolean, in, parent, unique, mail
     */
    public function check(string $name, $param, array $rules, string $table = null, string $column = null, string $error = null): bool
    {
        $this->name = $name;
        $this->errors = [];
        $this->defaultError = $error;

        // В текущем варианте перебора параметров возвращается ошибка при первом же несоблюдении какого-либо правила
        // Можно доработать, чтобы проверялись все правила, и возвращался сразу список несоответствий
        foreach ($rules as $key => $value) {
            if (is_int($key)) {
                if ($value === 'unique') {
                    if (!$this->unique($param, $table, $column)) {
                        return false;
                    }
                } elseif (!$this->$value($param)) {
                    return false;
                }
            } elseif (!$this->$key($param, $value)) {
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
    public function getError(): string
    {
        $message = '';

        if ($this->errors) {
            foreach ($this->errors as $error) {
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
    protected function string($param): bool
    {
        if (is_string($param)) {
            return true;
        }
        $this->addError($this->name . ' должен быть строкой');
        return false;
    }

    /**
     * Проверяет значение на целое число
     *
     * @param $param
     * @return bool
     */
    protected function int($param): bool
    {
        if (is_int($param)) {
            return true;
        }
        $this->addError($this->name . ' должен быть числом');
        return false;
    }

    /**
     * Проверяет строку на минимальную длину или int на минимальную величину
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected function min($param, $value): bool
    {
        if (is_int($param)) {
            if ($param >= $value) {
                return true;
            }
            $this->addError($this->name . ' должен быть больше или равен ' . $value);
            return false;
        }

        if (mb_strlen($param) >= $value) {
            return true;
        }
        $this->addError($this->name . ' должен быть больше или равен ' . $value . ' символов');
        return false;
    }

    /**
     * Проверяет строку на максимальную длину или int на максимальную величину
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected function max($param, $value): bool
    {
        if (is_int($param)) {
            if ($param <= $value) {
                return true;
            }
            $this->addError($this->name . ' должен быть меньше или равен ' . $value);
            return false;
        }

        if (mb_strlen($param) <= $value) {
            return true;
        }
        $this->addError($this->name . ' должен быть меньше или равен ' . $value . ' символов');
        return false;
    }

    /**
     * Проверяет корректность указанной почты
     *
     * @param $param
     * @return bool
     */
    protected function mail($param): bool
    {
        if (filter_var($param, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        $this->addError('Указана некорректная почта');
        return false;
    }

    /**
     * Проверяет на пустоту
     *
     * @param $param
     * @return bool
     */
    protected function required($param): bool
    {
        if ($param === null || $param === '') {
            $this->addError($this->name . ' не может быть пустым');
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
    protected function boolean($param): bool
    {
        if (is_bool($param)) {
            return true;
        }
        $this->addError($this->name . ' должен быть логическим типом');
        return false;
    }

    /**
     * Проверяет на строгое соответствие одному из указанных в массиве величин
     *
     * @param $param
     * @param array $values
     * @return bool
     */
    protected function in($param, array $values): bool
    {
        if (in_array($param, $values, true)) {
            return true;
        }

        $this->addError($this->name . ' указан некорректно');

        return false;
    }

    /**
     * Проверка по регулярному выражению
     *
     * @param $param
     * @param $value
     * @return bool
     */
    protected function parent($param, $value): bool
    {
        if (preg_match($value, $param)) {
            return true;
        }
        $this->addError($this->name . ' указан некорректно');
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
    private function unique($param, $table, $column): bool
    {
        if ($table === null || $column === null) {
            $this->addError('Не указана таблица или колонка для проверки');
            return false;
        }

        if (!$this->connection->query("SELECT $column FROM $table WHERE $column = ?", [['type' => 's', 'value' => $param]])) {
            return true;
        }

        $this->addError('Указанный ' .$this->name . ' уже существует, выберите другой');

        return false;
    }

    /**
     * Добавляет ошибку валидации
     *
     * @param string $error
     */
    private function addError(string $error): void
    {
        if ($this->defaultError) {
            $this->errors[] =$this->defaultError;
        } else {
            $this->errors[] = $error;
        }
    }
}
