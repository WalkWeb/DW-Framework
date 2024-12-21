<?php

use WalkWeb\NW\Loader\ImageCollection;

$this->title = 'Загрузка картинки';

echo "<h1>$this->title</h1>";

if (!empty($error)) {
    echo "<p>Ошибка: $error</p>";
}

/** @var ImageCollection $images */
if (!empty($images) && !empty($resizeImages)) {

    $i = 0;
    foreach ($images as $image) {
        echo '<p><img src="' . $image->getFilePath() . '" alt="" /></p>';
        echo '<p class="center"><img src="' . $resizeImages[$i] . '" alt="" class="resize" /></p>';
        $i++;
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
