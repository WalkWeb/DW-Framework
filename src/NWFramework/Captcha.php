<?php

namespace NW;

class Captcha
{
    public const INVALID_CAPTCHA = 'Символы с картинки указаны неверно';

    public static function getCaptchaImage(): string
    {
        $image = imagecreatetruecolor(150, 50);

        $image_color = imagecolorallocate($image, 30, 25, 21);
        imagefilledrectangle($image, 0, 0, 400, 50, $image_color);

        $font = 'fonts/11610.ttf';
        $letters = '1234567890';
        $length = 4;
        $font_size = 28;
        $height = 40;
        $captcha = '';

        for ($i = 0; $i < $length; $i++) {

            // дописываем случайный символ из алфавила
            $captcha .= $letters[Tools::rand(0, strlen($letters) - 1)];

            // растояние между символами
            $x = 20 + 30 * $i;

            // случайное смещение
            $x = Tools::rand($x, $x + 4);

            // координата Y
            $y = $height - (($height - $font_size) / 2);

            // цвет для текущей буквы
            $curcolor = imagecolorallocate($image, Tools::rand(100, 200), Tools::rand(100, 200), Tools::rand(100, 200));

            // случайный угол наклона
            $angle = Tools::rand(-45, 45);

            // вывод текста
            imagettftext($image, $font_size, $angle, $x, $y, $curcolor, $font, $captcha[$i]);
        }

        Session::setParam('captcha', md5($captcha . KEY));

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
}
