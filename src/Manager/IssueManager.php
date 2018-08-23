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
    
    public function __construct(Client $client, SessionInterface $session)
    {
        $this->client = $client;
        $this->session = $session;
        $this->authenticate();
        $this->pager = new ResultPager($this->client);
    }

    public function authenticate()
    {
        $this->client->authenticate($this->session->get('access_token'), Client::AUTH_HTTP_TOKEN);
    }

    public function getIssues(int $page = 1)
    {
        $issues = [];
        $result = $this->pager->fetch($this->client->currentUser(), 'issues', [
            ['per_page' => 4, 'page' => $page]
        ]);

        foreach ($result as $data) {
            $issues[] = $this->populateIssue($data);
        }

        return $issues;
    }

    public function getIssue(string $repoOwner, string $repoName, int $number)
    {
        $result = $this->client->issue()->show($repoOwner, $repoName, $number);
        $result['repository']['owner']['login'] = $repoOwner;
        $result['repository']['name'] = $repoName;

        return $this->populateIssue($result);
    }

    public function getComments(string $repoOwner, string $repoName, int $number)
    {
        $comments = [];
        $result = $this->client->issue()->comments()->all($repoOwner, $repoName, $number);

        foreach ($result as $data) {
            $comments[] = $this->populateComment($data);
        }

        return $comments;
    }

    private function populateIssue(array $data)
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

    private function populateComment(array $data)
    {
        $comment = new Comment();
        $comment->setAuthor($data['user']['login']);
        $comment->setAvatar($data['user']['avatar_url']);
        $comment->setBody($data['body']);
        $comment->setCreationDate($data['created_at']);

        return $comment;
    }

    public function parseLastPageNumber()
    {
        $pagination = $this->pager->getPagination();

        if (array_key_exists('last', $pagination)) {
            $parts = parse_url($pagination['last']);
            parse_str($parts['query'], $query);

            return (int)$query['page'];
        }

        return null;
    }
}