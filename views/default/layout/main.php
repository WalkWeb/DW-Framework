<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= htmlspecialchars($this->title) ?></title>
    <meta name="Description" content="<?= htmlspecialchars($this->description) ?>">
    <meta name="Keywords" content="<?= htmlspecialchars($this->keywords) ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="menu">
    <ul class="navigation">
        <li><a href="/" title="">Главная</a></li>
        <li><a href="/posts/1" title="">Посты</a></li>
        <li><a href="/post/create" title="">Создать пост</a></li>
        <li><a href="/cookies" title="">Cookies</a></li>
        <li><a href="/image" title="">Загрузка картинки</a></li>
        <li><a href="/registration" title="">Регистрация</a></li>
    </ul>
</div>
<div class="content">
    <?= $content ?? 'Отсутствует контент для отображения' ?>
    <hr color="#444">
</div>
</body>
</html>