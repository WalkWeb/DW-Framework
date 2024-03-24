<?php

use Models\Post\LegacyPost;
use NW\AppException;

$this->title = 'Сервер получил POST-данные';

/**
 * @var LegacyPost $post
 */
if (empty($post)) {
    // Такой ситуации не должно произойти, но на всякий случай проверяем (и чтобы phpStorm не ругался)
    throw new AppException('Ошибка: нет данных по посту');
}

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<p>ID: <?= $post->getId() ?></p>

<p>Заголовок: <?= $post->getTitle() ?></p>

<p>Slug: <?= $post->getSlug() ?></p>

<p>Содержимое: <?= $post->getText() ?></p>
