<?php

declare(strict_types=1);

namespace Models\Post;

use NW\AppException;
use NW\Container;
use NW\Response;

class PostRepository
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $slug
     * @return PostInterface
     * @throws AppException
     * @throws PostException
     */
    public function get(string $slug): PostInterface
    {
        $data =  $this->container->getConnectionPool()->getConnection()->query(
            'SELECT `id`, `title`, `slug`, `text`, `created_at`, `updated_at` FROM `posts` WHERE `slug` = ?',
            [['type' => 's', 'value' => $slug]],
            true
        );

        if (!$data) {
            throw new PostException(PostException::NOT_FOUND, Response::NOT_FOUND);
        }

        return PostFactory::createFromDB($data);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return array
     * @throws AppException
     */
    public function getList(int $offset, int $limit): array
    {
        $postList = [];
        $data = $this->container->getConnectionPool()->getConnection()->query(
            'SELECT `id`, `title`, `slug`, `text`, `created_at`, `updated_at` FROM `posts` ORDER BY `created_at` DESC LIMIT ? OFFSET ?',
            [
                ['type' => 'i', 'value' => $limit],
                ['type' => 'i', 'value' => $offset],
            ],
        );

        foreach ($data as $datum) {
            $postList[] = PostFactory::createFromDB($datum);
        }

        return $postList;
    }

    /**
     * @return int
     * @throws AppException
     */
    public function getTotalCount(): int
    {
        return $this->container->getConnectionPool()->getConnection()->query(
            'SELECT count(`id`) as `total` FROM `posts`',
            [],
            true
        )['total'] ?: 0;
    }
}
