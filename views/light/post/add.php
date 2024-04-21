<?php

$this->title = 'Добавить новый пост';
$postAction = '/post/create';

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<p><?= htmlspecialchars($message ?? '') ?></p>

<form method="POST" action="<?= $postAction ?>">
    <label><input name="title" autocomplete="off" value="<?= htmlspecialchars($title ?? '') ?>"></label>

    <label><textarea name="text" autocomplete="off"><?= htmlspecialchars($text ?? '') ?></textarea></label>

    <label><input type="hidden" name="csrf" value="<?= $csrfToken ?? '' ?>"></label>

    <img src="<?= $captcha ?? '/images/no_captcha.png' ?>" alt="captcha"/>
    <label><input name="captcha" autocomplete="off"></label>

    <button>Создать пост</button>
</form>
