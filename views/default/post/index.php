<?php

$this->title = 'Посты';

?>

<h1><?= htmlspecialchars($this->title)?></h1>

<?php

if (!empty($posts)) {
    foreach ($posts as $post) {
        $url = '/post/' . $post['id'];
        echo '
            <h2><a href="' . $url . '">' . htmlspecialchars($post['title']) . '</a></h2>
            <p>' . htmlspecialchars($post['text']) . '</p>';
    }
} else {
    echo 'На сайте пока нет постов';
}

?>

<hr color="#444">

<div class="pagination">
    <?= $pagination ?? '' ?>
</div>
