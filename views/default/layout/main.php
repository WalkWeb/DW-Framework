<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= htmlspecialchars($this->title) ?></title>
    <meta name="Description" content="<?= htmlspecialchars($this->description) ?>">
    <meta name="Keywords" content="<?= htmlspecialchars($this->keywords) ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="content">
    <?= $content ?? 'Отсутствует контент для отображения' ?>
    <?php if (!APPLICATION_OFFLINE && DEV) {echo NW\Runtime::end();} ?>
</div>
</body>
</html>