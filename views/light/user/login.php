<?php

use Handler\User\LoginPageHandler;

$this->title = 'Вход';
$postAction = '/login';

// TODO Add csrf-token

echo '<h1>' . htmlspecialchars($this->title) . '</h1>';

// При получении обычный ошибки - выводим и ошибку и форму входа. При ошибки о том, что пользователь уже авторизован - только ошибку

if (!empty($error)) {
    if ($error === LoginPageHandler::ALREADY_AUTH) {
        echo "<p>$error</p>";
    } else {
        echo "<p>$error</p>";

        echo '<form method="POST" action="' .$postAction . '">
                <label>Login:<br /><input name="login" autocomplete="off" value=""></label>
                <label>Password:<br /><input name="password" autocomplete="off" value="" type="password"></label>
            
                <button>Войти</button>
            </form>';
    }
} else {
    echo '<form method="POST" action="' .$postAction . '">
            <label>Login:<br /><input name="login" autocomplete="off" value=""></label>
            <label>Password:<br /><input name="password" autocomplete="off" value="" type="password"></label>
        
            <button>Войти</button>
        </form>';
}
