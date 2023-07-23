<?php

namespace NW;

use Throwable;

class Tools
{
    /**
     * Генерирует случайную строку
     *
     * TODO вынести в trait StringUtils и от данного класса вообще можно отказаться
     *
     * @param int $length
     * @return string
     */
    public static function getRandStr(int $length = 15): string
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
     * TODO На удаление
     *
     * @param int $from
     * @param int $before
     * @return int
     */
    public static function rand(int $from, int $before): int
    {
        try {
            $int = random_int($from, $before);
        } catch (Throwable $e) {
            $int = rand($from, $before);
        }

        return $int;
    }

    /**
     * Возвращает текущую дату и время
     *
     * TODO На удаление
     *
     * @param string $format
     * @return string
     */
    public static function getNowDateTime($format = 'Y-m-d H:i:s'): string
    {
        return date($format);
    }
}
