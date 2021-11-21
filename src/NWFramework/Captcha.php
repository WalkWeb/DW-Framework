<?php

namespace NW;

// TODO Уйти от статики

class Captcha
{
    public const INVALID_CAPTCHA = 'Символы с картинки указаны неверно';

    /**
     * @var string
     */
    private static $captcha = '';

    public static function getCaptchaImage(
        int $width_image = 150,
        int $height_image = 50,
        string $letters = '1234567890',
        int $length = 4,
        int $font_size = 28): string
    {
        $image = imagecreatetruecolor($width_image, $height_image);
        $image_color = imagecolorallocate($image, 30, 25, 21);
        imagefilledrectangle($image, 0, 0, 400, 50, $image_color);
        $font = DIR . '/public/fonts/11610.ttf';
        $height = 40;
        self::$captcha = '';

        for ($i = 0; $i < $length; $i++) {

            // Дописываем случайный символ
            self::$captcha .= $letters[Tools::rand(0, strlen($letters) - 1)];

            // Растояние между символами
            $x = 20 + 30 * $i;

            // Случайное смещение
            $x = Tools::rand($x, $x + 4);

            // Координата Y
            $y = $height - (($height - $font_size) / 2);

            // Цвет для текущей буквы
            $curcolor = imagecolorallocate($image, Tools::rand(100, 200), Tools::rand(100, 200), Tools::rand(100, 200));

            // Случайный угол наклона
            $angle = Tools::rand(-45, 45);

            // Вывод текста
            imagettftext($image, $font_size, $angle, $x, $y, $curcolor, $font, self::$captcha[$i]);
        }

        Session::setParam('captcha', md5(self::$captcha . KEY));

        ob_start();
        imagepng($image);
        $image = ob_get_contents();
        ob_end_clean();
        $imageData = base64_encode($image);
        return "data:image/png;base64,{$imageData}";
    }

    /**
     * Проверяет корректность указанной капчи
     *
     * @param string $captcha
     * @return bool
     */
    public static function checkCaptcha(string $captcha): bool
    {
        $formCaptcha = md5($captcha . KEY);
        $sessionCaptcha = Session::getParam('captcha');
        return $formCaptcha === $sessionCaptcha;
    }

    /**
     * Возвращает сгенерированную капчу
     *
     * @return string
     */
    public static function getCaptcha(): string
    {
        return self::$captcha;
    }
}
