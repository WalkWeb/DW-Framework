<?php

use Models\Post\PostInterface;

$this->title = 'Посты';

?>

<h1><?= htmlspecialchars($this->title) ?></h1>

<?php

/**
 * @var PostInterface[] $posts
 */
if (!empty($posts)) {
    foreach ($posts as $post) {
        $url = '/post/' . $post->getSlug();
        echo '
            <h2><a href="' . $url . '">' . htmlspecialchars($post->getTitle()) . '</a></h2>
            <p>' . htmlspecialchars($post->getText()) . '</p>';
    }
    echo '<hr color="#444">';
} else {
    echo '<p>На сайте пока нет постов</p>';
}

?>

<div class="pagination">
    <?= $pagination ?? '' ?>
</div>
