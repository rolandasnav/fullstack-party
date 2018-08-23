<?php

namespace App\Controller;

use App\Manager\IssueManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends Controller
{
    private $issueManager;

    public function __construct(IssueManager $issueManager)
    {
        $this->issueManager = $issueManager;
    }

    /**
     * @Route("/issues/{page}", defaults={"page"=1}, name="issues")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function issues(int $page)
    {
        $issues = $this->issueManager->getIssues($page);
        $lastPage = $this->issueManager->parseLastPageNumber();

        return $this->render('Issue/issues.html.twig', [
            'issues' => $issues,
            'currentPage' => $page,
            'lastPage' => $lastPage ? $lastPage : $page
        ]);
    }

    /**
     * @Route("/issue/{repoOwner}/{repoName}/{number}", name="issue")
     * @param int $number
     * @param string $repoName
     * @param string $repoOwner
     */
    public function issue(string $repoOwner, string $repoName, int $number)
    {
        $issue = $this->issueManager->getIssue($repoOwner, $repoName, $number);
        $comments = $this->issueManager->getComments($repoOwner, $repoName, $number);

        return $this->render('Issue/issue.html.twig', [
            'issue' => $issue,
            'comments' => $comments
        ]);
    }
}