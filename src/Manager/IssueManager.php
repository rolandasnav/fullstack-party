<?php

namespace App\Manager;


use App\Model\Comment;
use App\Model\Issue;
use App\Model\IssuePager;
use Github\Client;
use Github\ResultPager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IssueManager
{
    private $session;
    private $client;
    private $pager;

    /**
     * IssueManager constructor.
     * @param Client $client
     * @param SessionInterface $session
     */
    public function __construct(Client $client, SessionInterface $session)
    {
        $this->client = $client;
        $this->session = $session;
        $this->authenticate();
        $this->pager = new ResultPager($this->client);
    }

    /**
     * Authenticates the user sending the request
     */
    public function authenticate(): void
    {
        $this->client->authenticate($this->session->get('access_token'), Client::AUTH_HTTP_TOKEN);
    }

    /**
     * Returns the first or specific page of users' issues
     *
     * @param int $page
     * @return array
     */
    public function getIssues(int $page = 1): array
    {
        $issues = [];
        $result = $this->pager->fetch($this->client->currentUser(), 'issues', [
            ['per_page' => 4, 'page' => $page, 'state' => 'all']
        ]);

        foreach ($result as $data) {
            $issues[] = $this->populateIssue($data);
        }

        return $issues;
    }

    /**
     * Returns an issue
     *
     * @param string $repoOwner
     * @param string $repoName
     * @param int $number
     * @return Issue
     */
    public function getIssue(string $repoOwner, string $repoName, int $number): Issue
    {

        $result = $this->client->issue()->show($repoOwner, $repoName, $number);
        $result['repository']['owner']['login'] = $repoOwner;
        $result['repository']['name'] = $repoName;

        return $this->populateIssue($result);
    }

    /**
     * Returns all the comments, which belong to an issue
     *
     * @param string $repoOwner
     * @param string $repoName
     * @param int $number
     * @return array
     */
    public function getComments(string $repoOwner, string $repoName, int $number): array
    {
        $comments = [];
        $result = $this->client->issue()->comments()->all($repoOwner, $repoName, $number);

        foreach ($result as $data) {
            $comments[] = $this->populateComment($data);
        }

        return $comments;
    }

    /**
     * Returns the users' open issue count
     *
     * @return int
     */
    public function getOpenIssueCount(): int
    {
        return count($this->client->currentUser()->issues(['state' => 'open']));
    }

    /**
     * Returns the users' closed issue count
     *
     * @return int
     */
    public function getClosedIssueCount(): int
    {
        return count($this->client->currentUser()->issues(['state' => 'closed']));
    }

    /**
     * Populates the Issue object with data received from the API
     *
     * @param array $data
     * @return Issue
     */
    private function populateIssue(array $data): Issue
    {
        $issue = new Issue();
        $issue->setNumber($data['number']);
        $issue->setTitle($data['title']);
        $issue->setBody($data['body']);
        $issue->setAuthor($data['user']['login']);
        $issue->setLabels($data['labels']);
        $issue->setState($data['state']);
        $issue->setComments($data['comments']);
        $issue->setCreationDate($data['created_at']);
        $issue->setRepoOwner($data['repository']['owner']['login']);
        $issue->setRepoName($data['repository']['name']);

        return $issue;
    }

    /**
     * Populates the Comment object with data received from the API
     *
     * @param array $data
     * @return Comment
     */
    private function populateComment(array $data): Comment
    {
        $comment = new Comment();
        $comment->setAuthor($data['user']['login']);
        $comment->setAvatar($data['user']['avatar_url']);
        $comment->setBody($data['body']);
        $comment->setCreationDate($data['created_at']);

        return $comment;
    }

    /**
     * Returns the last page number or null if already on it
     *
     * @return int|null
     */
    public function parseLastPageNumber(): ?int
    {
        $pagination = $this->pager->getPagination();

        if ($pagination && array_key_exists('last', $pagination)) {
            $parts = parse_url($pagination['last']);
            parse_str($parts['query'], $query);

            return (int)$query['page'];
        }

        return null;
    }
}