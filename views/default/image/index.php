<?php

use NW\Loader\Image;

$this->title = 'Загрузка картинки';

echo "<h1>$this->title</h1>";

if (!empty($error)) {
    echo "<p>Ошибка: $error</p>";
}

/** @var Image[] $images */
if (!empty($images)) {
    foreach ($images as $image) {
        echo '<p><img src="' . $image->getFilePath() . '" alt="" /></p>';
    }
}

?>

<form enctype="multipart/form-data" method="post" action="/image">
    <input type="file" name="file">
    <button>Загрузить одну картинку</button>
</form>
<br /><br />
<form enctype="multipart/form-data" method="post" action="/image_multiple">
    <input type="file" name="file[]" multiple>
    <button>Загрузить несколько картинок</button>
</form>
<br /><br />