<?php

declare(strict_types=1);

namespace Tests\Model\Post;

use Exception;
use Models\Post\PostException;
use Models\Post\PostFactory;
use Models\Post\PostInterface;
use NW\AppException;
use NW\Traits\StringTrait;
use Tests\AbstractTestCase;

class PostFactoryTest extends AbstractTestCase
{
    use StringTrait;

    /**
     * Тест на успешное создание поста из данных создания поста на сайте
     *
     * @throws AppException
     */
    public function testPostFactoryCreateSuccess(): void
    {
        $data = [
            'title' => 'Заголовок поста',
            'text' => 'Содержимое поста',
        ];

        $post = PostFactory::createFromForm($data);

        self::assertEquals(36, strlen($post->getId()));
        self::assertEquals($data['title'], $post->getTitle());
        self::assertRegExp('/zagolovok posta-/', $post->getSlug()); // TODO пробел в slug заменять на "-"
        self::assertEquals($data['text'], $post->getText());
    }

    /**
     * Тесты на различные варианты невалидных данных
     *
     * @dataProvider failDataProvider
     * @param array $data
     * @param string $error
     */
    public function testPostFactoryCreateFail(array $data, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        PostFactory::createFromForm($data);
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function failDataProvider(): array
    {
        return [
            // Отсутствует title
            [
                [
                    'text' => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE,
            ],
            // title некорректного типа
            [
                [
                    'title' => true,
                    'text' => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE,
            ],
            // title меньше минимальной длинны
            [
                [
                    'title' => self::generateString(PostInterface::TITLE_MIN_LENGTH - 1),
                    'text' => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE_VALUE . PostInterface::TITLE_MIN_LENGTH . '-' . PostInterface::TITLE_MAX_LENGTH,
            ],
            // title больше максимальной длинны
            [
                [
                    'title' => self::generateString(PostInterface::TITLE_MAX_LENGTH + 1),
                    'text' => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE_VALUE . PostInterface::TITLE_MIN_LENGTH . '-' . PostInterface::TITLE_MAX_LENGTH,
            ],
            // Отсутствует text
            [
                [
                    'title' => 'Заголовок поста',
                ],
                PostException::INVALID_TEXT,
            ],
            // text некорректного типа
            [
                [
                    'title' => 'Заголовок поста',
                ],
                PostException::INVALID_TEXT,
            ],
            // text меньше минимальной длинны
            [
                [
                    'title' => 'Заголовок поста',
                    'text' => self::generateString(PostInterface::TEXT_MIN_LENGTH - 1),
                ],
                PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH,
            ],
            // text больше максимальной длинны
            [
                [
                    'title' => 'Заголовок поста',
                    'text' => self::generateString(PostInterface::TEXT_MAX_LENGTH + 1),
                ],
                PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH,
            ],
        ];
    }
}
