<?php

declare(strict_types = 1);

enum Navigate 
{
    case Previous;
    case Next;
}

class ArticleController
{
    private DatabaseManager $databaseManager;

    public function __construct()
    {
        global $config;
        $this->databaseManager = new DatabaseManager($config['host'], $config['user'], $config['password'], $config['dbname']);
        $this->databaseManager->connect();
    }

    public function index()
    {
        // Load all required data
        $articles = $this->getArticles();

        // Load the view
        require 'View/articles/index.php';
    }

    // Note: this function can also be used in a repository - the choice is yours
    private function getArticles(): array
    {
        // TODO: prepare the database connection
        // Note: you might want to use a re-usable databaseManager class - the choice is yours

        // TODO: fetch all articles as $rawArticles (as a simple array)
        $rawArticles = [];

        $query = 'SELECT * FROM articles';

        $statementObj = $this->databaseManager->connection->prepare($query);
        $statementObj->execute();

        $statementObj->setFetchMode(PDO::FETCH_ASSOC);
        $rawArticles = $statementObj->fetchAll();

        $this->databaseManager->disconnect();

        $articles = [];
        foreach ($rawArticles as $rawArticle) {
            // We are converting an article from a "dumb" array to a much more flexible class
            $articles[] = new Article((int)$rawArticle['ID'], $rawArticle['title'], $rawArticle['description'], $rawArticle['publish_date']);
        }

        return $articles;
    }

    public function showNextPreviousArticle(int $currentId, Navigate $navigate)
    {
        switch ($navigate) {
            case Navigate::Next:
                $query = 'SELECT COUNT(ID) AS disable, MIN(ID) AS ID FROM articles WHERE ID>:id;';
                break;
            case Navigate::Previous:
                $query = 'SELECT COUNT(ID) AS disable, MAX(ID) AS ID FROM articles WHERE ID<:id;';
        }

        $statementObj = $this->databaseManager->connection->prepare($query);
        $statementObj->bindValue(':id', $currentId, PDO::PARAM_INT);
        $statementObj->execute();
        $statementObj->setFetchMode(PDO::FETCH_ASSOC);
        $result = $statementObj->fetch();

        if ($result['ID'] === NULL) {
            $this->showArticle($currentId);
        } else {
            $this->showArticle((int)$result['ID']);
        }
    }

    public function showArticle(int $id, bool $disablePrevious = false, bool $disableNext = false)
    {
        $disablePrevious;
        $disableNext;
        $article = $this->getArticle($id);
        require 'View/articles/show.php';
    }

    private function getArticle(int $id): Article
    {
        // TODO: this can be used for a detail page
        $query = 'SELECT * FROM articles WHERE ID=:id;';

        $statementObj = $this->databaseManager->connection->prepare($query);
        $statementObj->bindValue(':id', $id, PDO::PARAM_INT);
        $statementObj->execute();
        $statementObj->setFetchMode(PDO::FETCH_ASSOC);
        $rawArticle = $statementObj->fetch();
        return new Article((int)$rawArticle['ID'], $rawArticle['title'], $rawArticle['description'], $rawArticle['publish_date']);
    }
}