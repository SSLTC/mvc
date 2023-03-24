<?php require 'View/includes/header.php'?>

<?php // Use any data loaded in the controller here ?>

<section>
    <h1><?= $article->title . ' - ' . $article->getName() ?></h1>
    <p><?= $article->formatPublishDate() ?></p>
    <p><?= $article->description ?></p>

    <?php // TODO: links to next and previous ?>
    <nav aria-label="...">
        <ul class="pagination">
            <li class="page-item <?= $disablePrevious ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=show-previous-article&id=<?= $article->id ?>">Previous</a>
            </li>
            <li class="page-item <?= $disableNext ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=show-next-article&id=<?= $article->id ?>">Next</a>
            </li>
        </ul>
    </nav>
</section>

<?php require 'View/includes/footer.php'?>
