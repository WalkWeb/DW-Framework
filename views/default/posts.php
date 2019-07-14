<?php

$this->title = 'Посты';

?>

<h1><?= htmlspecialchars($this->title)?></h1>

<?php

if (!empty($posts)) {
    foreach ($posts as $post) {
        echo '<h2>[' . $post['id'] . '] ' . $post['title'] . '</h2><p>' . $post['text'] . '</p>';
    }
} else {
    echo 'На сайте пока нет постов';
}
