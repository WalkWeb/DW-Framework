<?php

declare(strict_types=1);

namespace Tests\src;

use Tests\AbstractTest;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Translation;

class TranslationTest extends AbstractTest
{
    /**
     * Тест на стандартный успешный перевод сообщения
     *
     * @throws AppException
     */
    public function testTranslationTransSuccess(): void
    {
        $language = 'ru';
        $message = 'Home';
        $messages = ['Home' => 'Главная'];

        $translation = new Translation($this->getContainer(), $language, $messages);
        self::assertEquals('Главная', $translation->trans($message));
    }

    /**
     * @throws AppException
     */
    public function testTranslationTransFromFile(): void
    {
        $language = 'ru';
        $message = 'Home';

        $translation = new Translation($this->getContainer(), $language);

        self::assertEquals('Главная', $translation->trans($message));
    }

    /**
     * Тест на ситуацию, когда указан неизвестный язык - берется перевод из языка по умолчанию (en)
     *
     * @throws AppException
     */
    public function testTranslationTransDefaultLanguage(): void
    {
        $language = 'xx';
        $message = 'Home';

        $translation = new Translation($this->getContainer(), $language);

        self::assertEquals('Home', $translation->trans($message));
    }

    /**
     * Тест ситуации, когда указанного сообщения нет в справочнике по переводу, и вернулось это же сообщение
     *
     * @throws AppException
     */
    public function testTranslationTransUndefinedMessage(): void
    {
        $language = 'ru';
        $message = 'Unknown message';
        $messages = [];

        $translation = new Translation($this->getContainer(), $language, $messages);

        self::assertEquals($message, $translation->trans($message));
    }

    /**
     * Тест ситуации, когда справочник переводов составлен некорректно, и вместо строки получен какой-то другой тип
     * сообщения
     *
     * @throws AppException
     */
    public function testTranslationTransInvalidMessage(): void
    {
        $language = null;
        $messages = [
            'hello' => [],
        ];

        $translation = new Translation($this->getContainer(), $language, $messages);

        self::assertEquals('hello', $translation->trans('hello'));
    }

    /**
     * Тест исключительной ситуации, когда передан язык по умолчанию, но справочник по переводу отсутствует
     *
     * @throws AppException
     */
    public function testTranslationNoDefaultLanguage(): void
    {
        $language = 'xx';
        $defaultLanguage = 'yy';

        $this->expectException(AppException::class);
        $this->expectExceptionMessage('Default language messages file not found');
        new Translation($this->getContainer(), $language, null, $defaultLanguage);
    }

    /**
     * Тест определения языка на основании данных из $_SERVER['HTTP_ACCEPT_LANGUAGE']
     *
     * @throws AppException
     */
    public function testTranslationTransHttpAcceptLanguage(): void
    {
        $language = 'xxXX';
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $language;

        $translation = new Translation($this->getContainer());

        self::assertEquals('xx', $translation->getLanguage());
    }
}
