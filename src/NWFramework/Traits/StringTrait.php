<?php

declare(strict_types=1);

namespace NW\Traits;

use Exception;

trait StringTrait
{
    /**
     * Генерирует случайную строку
     *
     * @param int $length
     * @return string
     * @throws Exception
     */
    public static function generateString(int $length = 15): string
    {
        $chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[random_int(1, $numChars) - 1];
        }
        return $string;
    }
}
