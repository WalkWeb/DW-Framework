<?php

use NW\AppException;

$this->title = 'Сервер получил POST-данные';

if (empty($post)) {
    // Такой ситуации не должно произойти, но на всякий случай проверяем (и чтобы phpStorm не ругался)
    throw new AppException('Ошибка: нет данных по посту');
}

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<p>Заголовок: <?= $post->getTitle() ?></p>

<p>Содержимое: <?= $post->getText() ?></p>
