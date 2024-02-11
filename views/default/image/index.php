<?php

use NW\Loader\Image;

$this->title = 'Загрузка картинки';

echo "<h1>$this->title</h1>";

if (!empty($error)) {
    echo "<p>Ошибка: $error</p>";
}

/** @var Image $image */
if (!empty($image)) {
    echo '<p><img src="/images/upload/' . $image->getName() . $image->getType() . '" alt="" /></p>';
}

?>

<form enctype="multipart/form-data" method="post" action="/image">
    <input type="file" name="file">
    <button>Загрузить</button>
</form>

