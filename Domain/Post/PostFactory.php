<?php

declare(strict_types=1);

namespace Domain\Post;

use Exception;
use WalkWeb\NW\AppException;
use WalkWeb\NW\Traits\StringTrait;
use WalkWeb\NW\Traits\ValidationTrait;
use Ramsey\Uuid\Uuid;

class PostFactory
{
    use ValidationTrait;
    use StringTrait;

    /**
     * Создает объект Post на основании данных из формы создания поста на сайте
     *
     * @param array $data
     * @return PostInterface
     * @throws AppException
     */
    public static function createFromForm(array $data): PostInterface
    {
        try {
            $slugSuffix = '-' . random_int(10000, 99999);
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }

        return new Post(
            Uuid::uuid4()->toString(),
            self::validateTitle($data),
            strtolower(self::transliterate(strtr($data['title'], [' ' => '-', '.' => '', ',' => '',]))) . $slugSuffix,
            self::validateText($data),
        );
    }

    /**
     * Создает объект Post на основании данных из базы
     *
     * @param array $data
     * @return PostInterface
     * @throws AppException
     */
    public static function createFromDB(array $data): PostInterface
    {
        $slug = self::string($data, 'slug', PostException::INVALID_SLUG);

        self::stringMinMaxLength(
            $slug,
            PostInterface::SLUG_MIN_LENGTH,
            PostInterface::SLUG_MAX_LENGTH,
            PostException::INVALID_SLUG_VALUE . PostInterface::SLUG_MIN_LENGTH . '-' . PostInterface::SLUG_MAX_LENGTH
        );

        return new Post(
            self::uuid($data, 'id', PostException::INVALID_ID),
            self::validateTitle($data),
            $slug,
            self::validateText($data),
        );
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function validateTitle(array $data): string
    {
        $title = self::string($data, 'title', PostException::INVALID_TITLE);

        self::stringMinMaxLength(
            $title,
            PostInterface::TITLE_MIN_LENGTH,
            PostInterface::TITLE_MAX_LENGTH,
            PostException::INVALID_TITLE_VALUE . PostInterface::TITLE_MIN_LENGTH . '-' . PostInterface::TITLE_MAX_LENGTH
        );

        return $title;
    }

    /**
     * @param array $data
     * @return string
     * @throws AppException
     */
    private static function validateText(array $data): string
    {
        $text = self::string($data, 'text', PostException::INVALID_TEXT);

        self::stringMinMaxLength(
            $text,
            PostInterface::TEXT_MIN_LENGTH,
            PostInterface::TEXT_MAX_LENGTH,
            PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH
        );

        return $text;
    }
}
