<?php declare(strict_types=1);

class Author
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}