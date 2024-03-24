<?php

namespace NW;

use Exception;

class Captcha
{
    private Container $container;

    public const INVALID_CAPTCHA = 'Символы с картинки указаны неверно';

    private string $captcha = '';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param int $widthImage
     * @param int $heightImage
     * @param string $letters
     * @param int $length
     * @param int $fontSize
     * @return string
     * @throws AppException
     */
    public function getCaptchaImage(
        int $widthImage = 150,
        int $heightImage = 50,
        string $letters = '1234567890',
        int $length = 4,
        int $fontSize = 28): string
    {
        try {
            $image = imagecreatetruecolor($widthImage, $heightImage);
            $imageColor = imagecolorallocate($image, 30, 25, 21);
            imagefilledrectangle($image, 0, 0, 400, 50, $imageColor);
            $font = DIR . '/public/fonts/11610.ttf';
            $height = 40;

            for ($i = 0; $i < $length; $i++) {

                // Дописываем случайный символ
                $this->captcha .= $letters[random_int(0, strlen($letters) - 1)];

                // Расстояние между символами
                $x = 20 + 30 * $i;

                // Случайное смещение
                $x = random_int($x, $x + 4);

                // Координата Y
                $y = $height - (($height - $fontSize) / 2);

                // Цвет для текущей буквы
                $color = imagecolorallocate($image, random_int(100, 200), random_int(100, 200), random_int(100, 200));

                // Случайный угол наклона
                $angle = random_int(-45, 45);

                // Вывод текста
                imagettftext($image, $fontSize, $angle, $x, $y, $color, $font, $this->captcha[$i]);
            }

            Session::setParam('captcha', md5($this->captcha . KEY));

            ob_start();
            imagepng($image);
            $image = ob_get_clean();
            $imageData = base64_encode($image);
            return "data:image/png;base64,{$imageData}";
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }
    }

    /**
     * Проверяет корректность указанной капчи
     *
     * @param string $captcha
     * @return bool
     */
    public function checkCaptcha(string $captcha): bool
    {
        if ($this->container->getAppEnv() === Container::APP_TEST) {
            return true;
        }

        return md5($captcha . KEY) === Session::getParam('captcha');
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
