<?php

namespace NW;

class Tools
{
    /**
     * Генерирует случайную строку
     *
     * @param integer $length
     * @return string
     */
    public static function getRandStr($length = 15): string
    {
        $chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[self::rand(1, $numChars) - 1];
        }
        return $string;
    }

    /**
     * Так как использовать rand() уже не по феншую, а random_int бросает исключения - чтобы их постоянно не
     * обрабатывать, выношу в отдельный метод
     *
     * @param int $from
     * @param int $before
     * @return int
     */
    public static function rand(int $from, int $before): int
    {
        try {
            $int = random_int($from, $before);
        } catch (\Throwable $e) {
            $int = rand($from, $before);
        }

        return $int;
    }

    /**
     * Проверяет, является ли значение числом по существу - т.е. 10 или '10' вернет true
     *
     * @param $value
     * @return bool
     */
    public static function validateInt($value): bool
    {
        $value = filter_var($value, FILTER_VALIDATE_INT);
        return is_int($value);
    }

    /**
     * Возвращает текущую дату и время
     *
     * @param string $format
     * @return string
     */
    public static function getNowDateTime($format = 'Y-m-d H:i:s'): string
    {
        return (new \DateTime())->format($format);
    }
}
