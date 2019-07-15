<?php

$this->title = 'Добавить новый пост';

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<form method="POST" action="/post/create">
    <label><input name="title" autocomplete="off"></label>

    <label><textarea name="text" autocomplete="off"></textarea>

    <button>Создать пост</button>
</form>
