<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= htmlspecialchars($error ?? 'Неизвестная ошибка') ?></title>
    <meta name="Description" content="">
    <meta name="Keywords" content="">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="text/css" href="/styles/main.css">
</head>
<body>
<div class="content">
    <h1><?= htmlspecialchars($error ?? 'Неизвестная ошибка') ?></h1>
</body>
</html>