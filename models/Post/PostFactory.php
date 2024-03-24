<?php

declare(strict_types=1);

namespace Models\Post;

use Exception;
use NW\AppException;
use NW\Traits\StringTrait;
use NW\Traits\ValidationTrait;
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
        self::string($data, 'title', PostException::INVALID_TITLE);
        self::string($data, 'text', PostException::INVALID_TEXT);

        self::stringMinMaxLength(
            $data['title'],
            PostInterface::TITLE_MIN_LENGTH,
            PostInterface::TITLE_MAX_LENGTH,
            PostException::INVALID_TITLE_VALUE . PostInterface::TITLE_MIN_LENGTH . '-' . PostInterface::TITLE_MAX_LENGTH
        );

        self::stringMinMaxLength(
            $data['text'],
            PostInterface::TEXT_MIN_LENGTH,
            PostInterface::TEXT_MAX_LENGTH,
            PostException::INVALID_TEXT_VALUE . PostInterface::TEXT_MIN_LENGTH . '-' . PostInterface::TEXT_MAX_LENGTH
        );

        try {
            $slugSuffix = '-' . random_int(10000, 99999);
        } catch (Exception $e) {
            throw new AppException($e->getMessage());
        }

        return new Post(
            Uuid::uuid4()->toString(),
            $data['title'],
            strtolower(self::transliterate($data['title'])) . $slugSuffix,
            $data['text'],
        );
    }
}
