<?php

use Models\User\UserInterface;

$this->title = 'Профиль';

echo '<h1>' . htmlspecialchars($this->title) . '</h1>';

/**
 * @var UserInterface $user
 */
if (empty($user)) {
    echo "<p>Вы не авторизованны</p>";
} else {
    $emailVerified = $user->isEmailVerified() ? 'да' : 'нет';
    $regComplete = $user->isRegComplete() ? 'да' : 'нет';

    echo "<p><b>ID</b>: {$user->getId()}<br />
    <b>Логин</b>: " . htmlspecialchars($user->getLogin()) . "<br />
    <b>Email</b>: " . htmlspecialchars($user->getEmail()) . "<br />
    <b>Email подтвержден?</b> $emailVerified<br />
    <b>Регистрация завершена?</b> $regComplete<br />
    <b>Шаблон</b>: " . htmlspecialchars($user->getTemplate()) . "<br />
    <b>Дата регистрации</b>: " . $user->getCreatedAt()->format('Y-m-d H:i:s') . "</p>";
}
