<?php

namespace Models;

use Models\Exceptions\PostException;
use NW\Response;

/**
 * Данный класс имитирует данные из базы по постам, чтобы можно было продемонстрировать работу маршрутов/контроллеров с
 * какими-то данными без необходимости подключаться к реальной базе и запрашивать реальные данные
 */
class PostDataProvider
{
    /**
     * @var array - эмуляция данных по постам
     */
    private static array $posts = [
        'post-1' => [
            'id'    => '0ca5ebd4-a498-490f-9821-f7518e8eecb1',
            'slug'  => 'post-1',
            'title' => 'Пост 1',
            'text'  => 'Содержимое первого поста',
        ],
        'post-2' => [
            'id'    => '0e62ab1a-6b21-4b6f-8012-a97ede1127f6',
            'slug'  => 'post-2',
            'title' => 'Пост 2',
            'text'  => 'Содержимое второго поста',
        ],
        'post-3' => [
            'id'    => '82f4b2ee-ddae-448b-ab42-b5779e5e13e3',
            'slug'  => 'post-3',
            'title' => 'Пост 3',
            'text'  => 'Содержимое третьего поста',
        ],
        'post-4' => [
            'id'    => 'ab471a0b-cdff-467b-b9ee-7c4f4b66f53e',
            'slug'  => 'post-4',
            'title' => 'Пост 4',
            'text'  => 'Содержимое четвертого поста',
        ],
        'post-5' => [
            'id'    => '8983b8e4-8160-404a-9e12-bc6821651bae',
            'slug'  => 'post-5',
            'title' => 'Пост 5',
            'text'  => 'Содержимое пятого поста',
        ],
        'post-6' => [
            'id'    => '1ac66a33-0b4e-410e-a836-21e55afe825b',
            'slug'  => 'post-6',
            'title' => 'Пост 6',
            'text'  => 'Содержимое шестого поста',
        ],
        'post-7' => [
            'id'    => 'c5559600-f341-4a50-8c14-75b4abca144f',
            'slug'  => 'post-7',
            'title' => 'Пост 7',
            'text'  => 'Содержимое седьмого поста',
        ],
        'post-8' => [
            'id'    => '10cf999c-4b8e-4462-83f5-f7760382d8a5',
            'slug'  => 'post-8',
            'title' => 'Пост 8',
            'text'  => 'Содержимое восьмого поста',
        ],
        'post-9' => [
            'id'    => 'd3f918ec-8fc2-4522-93dc-65e504984a3f',
            'slug'  => 'post-9',
            'title' => 'Пост 9',
            'text'  => 'Содержимое девятого поста',
        ],
        'post-10' => [
            'id'    => '1389c72f-cdff-4114-837a-24fe77b2c95b',
            'slug'  => 'post-10',
            'title' => 'Пост 10',
            'text'  => 'Содержимое десятого поста',
        ],
        'post-11' => [
            'id'    => 'ac56830f-b45b-4a57-984c-186d94d063ce',
            'slug'  => 'post-11',
            'title' => 'Пост 11',
            'text'  => 'Содержимое одиннадцатого поста',
        ],
    ];

    /**
     * @return int
     */
    public static function getPostsCount(): int
    {
        return count(self::$posts);
    }

    /**
     * @param string $id
     * @return object
     * @throws PostException
     */
    public static function getPostById(string $id): object
    {
        if (!array_key_exists($id, self::$posts)) {
            throw new PostException(PostException::NOT_FOUND, Response::NOT_FOUND);
        }

        return (object)self::$posts[$id];
    }

    /**
     * Возвращает указанное количество постов, начиная с указанной страницы
     *
     * На практике, посты выводятся с сортировкой (чаще всего) от даты, с указанным лимитом. Этот метод делает некоторое
     * отдаленное подобие такого запроса.
     *
     * @param int $page
     * @param int $limit
     * @return array
     * @throws PostException
     */
    public static function getPacksPost(int $page, int $limit = 5): array
    {
        if (self::$posts === [] && $page === 1) {
            return [];
        }

        if ($page > ceil(count(self::$posts) / $limit)) {
            throw new PostException(PostException::NOT_FOUND, Response::NOT_FOUND);
        }

        $offset = $page === 1 ? 0 : (($page - 1) * $limit);
        $limit += $offset;

        $packsPosts = [];

        $i = 0;
        foreach (self::$posts as $post) {
            if ($i >= $offset && $i < $limit) {
                $packsPosts[] = $post;
            }

            $i++;
        }

        return $packsPosts;
    }
}
