<?php

declare(strict_types=1);

namespace Tests\Domain\Post;

use Exception;
use Domain\Post\PostException;
use Domain\Post\PostFactory;
use Domain\Post\PostInterface;
use NW\AppException;
use Ramsey\Uuid\Uuid;
use Tests\AbstractTest;

class PostFactoryTest extends AbstractTest
{
    /**
     * Тест на успешное создание поста из данных создания поста на сайте
     *
     * @dataProvider createFormSuccessDataProvider
     * @param array $data
     * @throws AppException
     */
    public function testPostFactoryCreateFromFormSuccess(array $data): void
    {
        $post = PostFactory::createFromForm($data);

        self::assertEquals(36, strlen($post->getId()));
        self::assertTrue(Uuid::isValid($post->getId()));
        self::assertEquals($data['title'], $post->getTitle());
        self::assertRegExp('/zagolovok-posta-/', $post->getSlug());
        self::assertEquals($data['text'], $post->getText());
    }

    /**
     * Тесты на различные варианты невалидных данных
     *
     * @dataProvider createFormFailDataProvider
     * @param array $data
     * @param string $error
     */
    public function testPostFactoryCreateFromFormFail(array $data, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        PostFactory::createFromForm($data);
    }

    /**
     * Тест на успешное создание поста из данных из базы
     *
     * @dataProvider createDBSuccessDataProvider
     * @param array $data
     * @throws AppException
     */
    public function testPostFactoryCreateFromDBSuccess(array $data): void
    {
        $post = PostFactory::createFromDB($data);

        self::assertEquals($data['id'], $post->getId());
        self::assertEquals($data['title'], $post->getTitle());
        self::assertEquals($data['slug'], $post->getSlug());
        self::assertEquals($data['text'], $post->getText());
    }

    /**
     * Тест на невалидные варианты данных из базы
     *
     * @dataProvider createDBFailDataProvider
     * @param array $data
     * @param string $error
     * @throws AppException
     */
    public function testPostFactoryCreateFromDBFail(array $data, string $error): void
    {
        $this->expectException(AppException::class);
        $this->expectExceptionMessage($error);
        PostFactory::createFromDB($data);
    }

    /**
     * @return array
     *
     */
    public function createFormSuccessDataProvider(): array
    {
        return [
            [
                [
                    'title' => 'Заголовок поста',
                    'text'  => 'Содержимое поста',
                ],
            ],
        ];
    }

    /**
     * @return array
     *
     */
    public function createDBSuccessDataProvider(): array
    {
        return [
            [
                [
                    'id'    => 'd1ef5ba4-a087-4565-86fe-963c0b3b16b5',
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => 'Содержимое поста',
                ],
            ],
        ];
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function createFormFailDataProvider(): array
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
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE,
            ],
            // title меньше минимальной длинны
            [
                [
                    'title' => self::generateString(PostInterface::TITLE_MIN_LENGTH - 1),
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE_VALUE . PostInterface::TITLE_MIN_LENGTH . '-' . PostInterface::TITLE_MAX_LENGTH,
            ],
            // title больше максимальной длинны
            [
                [
                    'title' => self::generateString(PostInterface::TITLE_MAX_LENGTH + 1),
                    'text'  => 'Содержимое поста',
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
                    'text'  => self::generateString(PostInterface::TEXT_MIN_LENGTH - 1),
                ],
                PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH,
            ],
            // text больше максимальной длинны
            [
                [
                    'title' => 'Заголовок поста',
                    'text'  => self::generateString(PostInterface::TEXT_MAX_LENGTH + 1),
                ],
                PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH,
            ],
        ];
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function createDBFailDataProvider(): array
    {
        return [
            // Отсутствует id
            [
                [
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_ID
            ],
            // id некорректного типа
            [
                [
                    'id'    => null,
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_ID
            ],
            // id невалидный uuid
            [
                [
                    'id'    => 'ad448b57-2a4d-45b5-8b56-45be42a1bfd_',
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_ID_VALUE
            ],
            // Отсутствует title
            [
                [
                    'id'   => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'slug' => 'zagolovok posta-1234',
                    'text' => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE,
            ],
            // title некорректного типа
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => true,
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE,
            ],
            // title меньше минимальной длинны
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => self::generateString(PostInterface::TITLE_MIN_LENGTH - 1),
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE_VALUE . PostInterface::TITLE_MIN_LENGTH . '-' . PostInterface::TITLE_MAX_LENGTH,
            ],
            // title больше максимальной длинны
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => self::generateString(PostInterface::TITLE_MAX_LENGTH + 1),
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_TITLE_VALUE . PostInterface::TITLE_MIN_LENGTH . '-' . PostInterface::TITLE_MAX_LENGTH,
            ],
            // Отсутствует slug
            [
                [
                    'id'   => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'text' => 'Содержимое поста',
                ],
                PostException::INVALID_SLUG,
            ],
            // slug некорректного типа
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'slug'  => 1.1,
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_SLUG,
            ],
            // slug меньше минимальной длинны
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'slug'  => self::generateString(PostInterface::SLUG_MIN_LENGTH - 1),
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_SLUG_VALUE . PostInterface::SLUG_MIN_LENGTH . '-' . PostInterface::SLUG_MAX_LENGTH,
            ],
            // slug больше максимальной длинны
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'slug'  => self::generateString(PostInterface::SLUG_MAX_LENGTH + 1),
                    'text'  => 'Содержимое поста',
                ],
                PostException::INVALID_SLUG_VALUE . PostInterface::SLUG_MIN_LENGTH . '-' . PostInterface::SLUG_MAX_LENGTH,
            ],
            // Отсутствует text
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                ],
                PostException::INVALID_TEXT,
            ],
            // text некорректного типа
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                ],
                PostException::INVALID_TEXT,
            ],
            // text меньше минимальной длинны
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => self::generateString(PostInterface::TEXT_MIN_LENGTH - 1),
                ],
                PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH,
            ],
            // text больше максимальной длинны
            [
                [
                    'id'    => '2a666c9a-944d-4511-9011-74bfdbc43738',
                    'title' => 'Заголовок поста',
                    'slug'  => 'zagolovok posta-1234',
                    'text'  => self::generateString(PostInterface::TEXT_MAX_LENGTH + 1),
                ],
                PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH,
            ],
        ];
    }
}
