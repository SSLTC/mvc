<?php require 'View/includes/header.php'?>

<?php // Use any data loaded in the controller here ?>

<section>
    <h1><?= $article->title ?></h1>
    <p><?= $article->formatPublishDate() ?></p>
    <p><?= $article->description ?></p>

    <?php // TODO: links to next and previous ?>
    <a href="?page=show-previous-article&id=<?= $article->id ?>">Previous article</a>
    <a href="?page=show-next-article&id=<?= $article->id ?>">Next article</a>
</section>

<?php require 'View/includes/footer.php'?>
