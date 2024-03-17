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
        1  => ['id' => 1, 'title' => 'Заголовок первого поста', 'text' => 'Содержимое первого поста'],
        2  => ['id' => 2, 'title' => 'Заголовок второго поста', 'text' => 'Содержимое второго поста'],
        3  => ['id' => 3, 'title' => 'Заголовок третьего поста', 'text' => 'Содержимое третьего поста'],
        4  => ['id' => 4, 'title' => 'Заголовок четвертого поста', 'text' => 'Содержимое червертого поста'],
        5  => ['id' => 5, 'title' => 'Заголовок пятого поста', 'text' => 'Содержимое пятого поста'],
        6  => ['id' => 6, 'title' => 'Заголовок шестого поста', 'text' => 'Содержимое шестого поста'],
        7  => ['id' => 7, 'title' => 'Заголовок седьмого поста', 'text' => 'Содержимое седьмого поста'],
        8  => ['id' => 8, 'title' => 'Заголовок восьмого поста', 'text' => 'Содержимое восьмого поста'],
        9  => ['id' => 9, 'title' => 'Заголовок девятого поста', 'text' => 'Содержимое девятого поста'],
        10 => ['id' => 10, 'title' => 'Заголовок десятого поста', 'text' => 'Содержимое десятого поста'],
        11 => ['id' => 11, 'title' => 'Заголовок одиннадцатого поста', 'text' => 'Содержимое одиннадцатого поста'],
    ];

    /**
     * @return int
     */
    public static function getPostsCount(): int
    {
        return count(self::$posts);
    }

    /**
     * @param int $id
     * @return object
     * @throws PostException
     */
    public static function getPostById(int $id): object
    {
        if (empty(self::$posts[$id])) {
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
        $id = $page === 1 ? 1 : (($page - 1) * $limit + 1);

        if (empty(self::$posts[$id])) {
            throw new PostException(PostException::NOT_FOUND, Response::NOT_FOUND);
        }

        $i = 1;
        $packsPosts = [];

        while (true) {
            $packsPosts[$i] = self::$posts[$id];
            $id++;
            $i++;

            if ($i > $limit || empty(self::$posts[$id])) {
                break;
            }
        }

        return $packsPosts;
    }
}
