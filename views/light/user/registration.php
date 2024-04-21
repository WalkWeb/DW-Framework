<?php

$this->title = 'Регистрация';
$postAction = '/registration';

echo '<h1>' . htmlspecialchars($this->title) . '</h1>';

if (!empty($error)) {
    echo "<p>$error</p>";
}

?>

<form method="POST" action="<?= $postAction ?>">
    <label>Login:<br /><input name="login" autocomplete="off" value=""></label>
    <label>Email:<br /><input name="email" autocomplete="off" value=""></label>
    <label>Password:<br /><input name="password" autocomplete="off" value="" type="password"></label>
    <label><input type="hidden" name="csrf" value="<?= $csrfToken ?? '' ?>"></label>

    <button>Зарегистрироваться</button>
</form>
