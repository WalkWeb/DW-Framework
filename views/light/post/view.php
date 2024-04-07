<?php

use Domain\Post\PostInterface;
use NW\AppException;

/**
 * @var PostInterface $post
 */
if (empty($post)) {
    // Такой ситуации не должно произойти, но на всякий случай проверяем (и чтобы phpStorm не ругался)
    throw new AppException('Ошибка: нет данных по посту');
}

$this->title = htmlspecialchars($post->getTitle());
$firstPage = '/posts/1';

?>

<h1><?= htmlspecialchars($post->getTitle()) ?></h1>

<p><?= htmlspecialchars($post->getText()) ?></p>

<p><a href="<?= $firstPage ?>">Вернуться к списку постов</a></p>
