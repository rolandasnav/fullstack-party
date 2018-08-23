<?php

namespace App\Model;


class Issue
{
    private $number;
    private $title;
    private $body;
    private $author;
    private $labels;
    private $state;
    private $comments;
    private $creationDate;
    private $repoOwner;
    private $repoName;

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     */
    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    /**
     * @return array|null
     */
    public function getLabels(): ?array
    {
        return $this->labels;
    }

    /**
     * @param array $labels|null
     */
    public function setLabels(?array $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return int
     */
    public function getComments(): int
    {
        return $this->comments;
    }

    /**
     * @param int $comments
     */
    public function setComments(int $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param string $creationDate
     */
    public function setCreationDate(string $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return string
     */
    public function getRepoOwner(): string
    {
        return $this->repoOwner;
    }

    /**
     * @param string $repoOwner
     */
    public function setRepoOwner(string $repoOwner): void
    {
        $this->repoOwner = $repoOwner;
    }

    /**
     * @return string
     */
    public function getRepoName(): string
    {
        return $this->repoName;
    }

    /**
     * @param string $repoName
     */
    public function setRepoName(string $repoName): void
    {
        $this->repoName = $repoName;
    }
}