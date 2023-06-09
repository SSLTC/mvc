<?php

declare(strict_types=1);

require_once 'Model/Author.php';

class Article extends Author
{
    public int $id;
    public string $title;
    public ?string $description;
    public ?string $publishDate;

    public function __construct(int $id, string $title, string $authorName, 
                                ?string $description, ?string $publishDate)
    {
        $this->id = $id;
        $this->title = $title;
        $this->setName($authorName);
        $this->description = $description;
        $this->publishDate = $publishDate;
    }

    public function formatPublishDate($format = 'D d-M-Y'): string
    {
        // TODO: return the date in the required format
        $date = date_create($this->publishDate);
        return date_format($date, $format);
    }
}