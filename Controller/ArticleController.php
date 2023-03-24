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
    private bool $disablePrevious;
    private bool $disableNext;

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
                $query = 'SELECT MIN(ID) AS ID FROM articles WHERE ID>:id;';
                break;
            case Navigate::Previous:
                $query = 'SELECT MAX(ID) AS ID FROM articles WHERE ID<:id;';
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

    public function showArticle(int $id)
    {
        $article = $this->getArticle($id);
        $disablePrevious = $this->disablePrevious;
        $disableNext = $this->disableNext;
        require 'View/articles/show.php';
    }

    private function getArticle(int $id): Article
    {
        // TODO: this can be used for a detail page
        $query = 'SELECT * FROM (SELECT * FROM articles WHERE ID=:id) R1 JOIN 
        (SELECT COUNT(ID) AS hasPrev FROM articles WHERE ID<:id) R2 JOIN 
        (SELECT COUNT(ID) AS hasNext FROM articles WHERE ID>:id) R3;';

        $statementObj = $this->databaseManager->connection->prepare($query);
        $statementObj->bindValue(':id', $id, PDO::PARAM_INT);
        $statementObj->execute();
        $statementObj->setFetchMode(PDO::FETCH_ASSOC);
        $rawArticle = $statementObj->fetch();
        $this->disablePrevious = $rawArticle['hasPrev'] === 0? true : false;
        $this->disableNext = $rawArticle['hasNext'] === 0? true : false;
        return new Article((int)$rawArticle['ID'], $rawArticle['title'], $rawArticle['description'], $rawArticle['publish_date']);
    }
}