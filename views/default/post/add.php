<?php

$this->title = 'Добавить новый пост';

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<p><?= htmlspecialchars($message ?? '') ?></p>

<form method="POST" action="/post/create">
    <label><input name="title" autocomplete="off" value="<?= htmlspecialchars($title ?? '') ?>"></label>

    <label><textarea name="text" autocomplete="off"><?= htmlspecialchars($text ?? '') ?></textarea></label>

    <img src="<?= $captcha ?? '/images/no_captcha.png' ?>" alt="captcha" />
    <label><input name="captcha" autocomplete="off"></label>

    <button>Создать пост</button>
</form>
