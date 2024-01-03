<?php

namespace NW;

class Captcha
{
    public const INVALID_CAPTCHA = 'Символы с картинки указаны неверно';

    private string $captcha = '';

    public function getCaptchaImage(
        int $widthImage = 150,
        int $heightImage = 50,
        string $letters = '1234567890',
        int $length = 4,
        int $fontSize = 28): string
    {
        $image = imagecreatetruecolor($widthImage, $heightImage);
        $imageColor = imagecolorallocate($image, 30, 25, 21);
        imagefilledrectangle($image, 0, 0, 400, 50, $imageColor);
        $font = DIR . '/public/fonts/11610.ttf';
        $height = 40;

        for ($i = 0; $i < $length; $i++) {

            // Дописываем случайный символ
            $this->captcha .= $letters[Tools::rand(0, strlen($letters) - 1)];

            // Расстояние между символами
            $x = 20 + 30 * $i;

            // Случайное смещение
            $x = Tools::rand($x, $x + 4);

            // Координата Y
            $y = $height - (($height - $fontSize) / 2);

            // Цвет для текущей буквы
            $color = imagecolorallocate($image, Tools::rand(100, 200), Tools::rand(100, 200), Tools::rand(100, 200));

            // Случайный угол наклона
            $angle = Tools::rand(-45, 45);

            // Вывод текста
            imagettftext($image, $fontSize, $angle, $x, $y, $color, $font, $this->captcha[$i]);
        }

        Session::setParam('captcha', md5($this->captcha . KEY));

        ob_start();
        imagepng($image);
        $image = ob_get_clean();
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
    public function getCaptcha(): string
    {
        return $this->captcha;
    }
}
