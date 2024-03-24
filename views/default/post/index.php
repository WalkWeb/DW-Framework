<?php

$this->title = 'Посты';

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<?php

if (!empty($posts)) {
    foreach ($posts as $post) {
        $url = '/post/' . $post['slug'];
        echo '
            <h2><a href="' . $url . '">' . htmlspecialchars($post['title']) . '</a></h2>
            <p>' . htmlspecialchars($post['text']) . '</p>';
    }
    echo '<hr color="#444">';
} else {
    echo '<p>На сайте пока нет постов</p>';
}

?>

<div class="pagination">
    <?= $pagination ?? '' ?>
</div>
