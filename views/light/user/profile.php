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
    echo "<p><b>ID</b>: {$user->getId()}<br />";
    echo "<b>Логин</b>: " . htmlspecialchars($user->getLogin()) . "<br />";
    echo "<b>email</b>: " . htmlspecialchars($user->getEmail()) . "<br />";
    echo "<b>Шаблон</b>: " . htmlspecialchars($user->getTemplate()) . "<br />";
    echo "<b>Дата регистрации</b>: " . $user->getCreatedAt()->format('Y-m-d H:i:s') . "</p>";
}
