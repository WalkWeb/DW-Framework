<?php

$this->title = 'Cookies';

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<?php
    if (!empty($error)) {
        echo "<p class='red'>Error: $error</p>";
    }
?>

<table style="width: 50%;">
    <tr>
        <td><p>Key</p></td>
        <td><p>Value</p></td>
        <td></td>
    </tr>
    <?php
    if (!empty($cookies)) {
        foreach ($cookies as $key => $value) {
            echo '<tr><td><p>' . htmlspecialchars($key) . '</p></td><td><p>' . htmlspecialchars($value) . '</p></td><td>';
            echo '<form action="/cookies/delete" method="post"><input type="hidden" id="name" type="text" name="name" value="' . $key . '" /><input type="submit" class="delete" value="×" /></form>';
            echo '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="3"><p class="center">Cookies отсутствуют</p></td>';
    }
    ?>
</table>

<br/><br/>
<form action="/cookies/add" method="post">
    <label for="name">Name:</label>
    <input id="name" type="text" name="name" autocomplete="off"/>
    <label for="value">Value:</label>
    <input id="value" type="text" name="value" autocomplete="off"/>
    <button>Добавить</button>
</form>
