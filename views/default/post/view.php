<?php

use NW\AppException;

if (empty($post)) {
    // Такой ситуации не должно произойти, но на всякий случай проверяем (и чтобы phpStorm не ругался)
    throw new AppException('Ошибка: нет данных по посту');
}

$this->title = $post->title;
$firstPage = '/posts/1';

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<p><?= htmlspecialchars($post->text) ?></p>

<p><a href="<?= $firstPage ?>">Вернуться к списку постов</a></p>
