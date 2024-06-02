<?php

declare(strict_types=1);

namespace Tests\Domain\Post;

use Domain\Post\LegacyPost;
use Domain\Post\PostException;
use Exception;
use WalkWeb\NW\AppException;
use Ramsey\Uuid\Uuid;
use Tests\AbstractTest;

class LegacyPostTest extends AbstractTest
{
    /**
     * Тест на успешное создание LegacyPost
     *
     * @throws AppException
     */
    public function testLegacyPostCreateSuccess(): void
    {
        $id = 'fbd308c6-f81e-40bb-88ba-4a12fb27317b';
        $title = 'title';
        $text = 'text text text';

        $post = new LegacyPost($this->getContainer(), $id, $title, $text);

        self::assertEquals($id, $post->getId());
        self::assertEquals($title, $post->getTitle());
        self::assertEquals($text, $post->getText());
    }

    /**
     * Тесты на различные варианты некорректных данных
     *
     * @dataProvider failDataProvider
     * @param string $title
     * @param string $text
     * @param string $error
     * @throws PostException
     * @throws AppException
     */
    public function testLegacyPostCreateFail(string $title, string $text, string $error): void
    {
        $this->expectException(PostException::class);
        $this->expectExceptionMessage($error);
        new LegacyPost($this->getContainer(), Uuid::uuid4()->toString(), $title, $text);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function failDataProvider(): array
    {
        return [
            // title меньше минимальной длинны
            [
                '1234',
                'text text text',
                'Заголовок expected length >= 5',
            ],
            // title больше максимальной длинны
            [
                self::generateString(51),
                'text text text',
                'Заголовок expected length <= 50',
            ],
            // title содержит недопустимые символы
            [
                'title&',
                'text text text',
                'Заголовок does not match the pattern',
            ],
            // text меньше минимальной длинны
            [
                'title',
                '1234',
                'Содержимое поста expected length >= 5',
            ],
            // text больше максимальной длинны
            [
                'title',
                self::generateString(501),
                'Содержимое поста expected length <= 500',
            ],
        ];
    }
}
