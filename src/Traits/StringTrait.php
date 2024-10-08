<?php

declare(strict_types=1);

namespace WalkWeb\NW\Traits;

use Exception;
use WalkWeb\NW\AppException;

trait StringTrait
{
    /**
     * Генерирует случайную строку
     *
     * @param int $length
     * @return string
     * @throws AppException
     */
    public static function generateString(int $length = 15): string
    {
        try {
            $chars = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $numChars = strlen($chars);
            $string = '';
            for ($i = 0; $i < $length; $i++) {
                $string .= $chars[random_int(1, $numChars) - 1];
            }
            return $string;
        } catch (Exception $e) {
            throw new AppException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Метод на транслитерацию кириллицы в латиницу, плюс пробел заменяет прочерком, а точки и запятые удаляет
     *
     * Пример: "Перед началом установки" => "Pered-nachalom-ustanovki"
     *
     * @param string $string
     * @return string
     */
    public static function transliterate(string $string): string
    {
        $string = strtr($string, [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ]);

        return strtr($string, ['--' => '-']);
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    public static function jsonEncode(array $data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * @param string $json
     * @return array
     * @throws AppException
     */
    public static function jsonDecode(string $json): array
    {
        try {
            return (array)json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }
}
