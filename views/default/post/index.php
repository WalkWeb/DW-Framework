<?php

if (empty($post)) {
    // Такой ситуации не должно произойти, но на всякий случай проверяем (и чтобы phpStorm не ругался)
    die('Ошибка: нет данных по посту');
}

$this->title = $post->title;

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<p><?= htmlspecialchars($post->text) ?></p>

<p><a href="/post">Вернуться к списку постов</a></p>
