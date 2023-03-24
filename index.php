<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'Database/config.php';
require_once 'Database/DatabaseManager.php';

//include all your model files here
require 'Model/Article.php';
//include all your controllers here
require 'Controller/HomepageController.php';
require 'Controller/ArticleController.php';

// Get the current page to load
// If nothing is specified, it will remain empty (home should be loaded)
$page = $_GET['page'] ?? null;

// Load the controller
// It will *control* the rest of the work to load the page
switch ($page) {
    case 'articles-index':
        // This is shorthand for:
        // $articleController = new ArticleController;
        // $articleController->index();
        (new ArticleController())->index();
        break;
    case 'show-previous-article':
        (new ArticleController())->showArticle((int)$_GET['id'], Navigate::Previous);
        break;
    case'show-next-article':
        (new ArticleController())->showArticle((int)$_GET['id'], Navigate::Next);
        break;
    case 'show-article':
        // TODO: detail page
        (new ArticleController())->showArticle((int)$_GET['id'], Navigate::GivenID);
        break;
    case 'home':
    default:
        (new HomepageController())->index();
        break;
}