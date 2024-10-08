<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= htmlspecialchars($this->title) ?></title>
    <meta name="Description" content="<?= htmlspecialchars($this->description) ?>">
    <meta name="Keywords" content="<?= htmlspecialchars($this->keywords) ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/light.css">
</head>
<body>
<div class="menu">
    <ul class="navigation">
        <li><a href="/" title=""><?= $this->translate('Home') ?></a></li>
        <li><a href="/posts/1" title=""><?= $this->translate('Posts') ?></a></li>
        <li><a href="/post/create" title=""><?= $this->translate('Create Post') ?></a></li>
        <li><a href="/cookies" title=""><?= $this->translate('Cookies') ?></a></li>
        <li><a href="/image" title=""><?= $this->translate('Load Image') ?></a></li>
        <li><a href="/login" title=""><?= $this->translate('Login') ?></a></li>
        <li><a href="/registration" title=""><?= $this->translate('Registration') ?></a></li>
        <li><a href="/profile" title=""><?= $this->translate('Profile') ?></a></li>
        <li><a href="/logout" title=""><img src="/images/logout.png" class="logout" alt="" /></a></li>
    </ul>
</div>
<div class="content">
    <?= $content ?? 'Отсутствует контент для отображения' ?>
    <hr color="#444">
    <label>
        <?= $this->translate('Design') ?>:
        <select name="select" id="template">
            <option value="value2">default</option>
            <option value="value3" selected>light</option>
        </select>
    </label>
</div>
<script src="/js/main.js?v=1.00"></script>
</body>
</html>