<?php

namespace App\Controller;

use App\Manager\IssueManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IssueController extends Controller
{
    private $issueManager;

    /**
     * IssueController constructor.
     * @param IssueManager $issueManager
     */
    public function __construct(IssueManager $issueManager)
    {
        $this->issueManager = $issueManager;
    }

    /**
     * @Route("/issues/{page}", defaults={"page"=1}, name="issues")
     * @param int $page
     * @return Response
     */
    public function issues(int $page): Response
    {
        $issues = $this->issueManager->getIssues($page);
        $lastPage = $this->issueManager->parseLastPageNumber();
        $openIssueCount = $this->issueManager->getOpenIssueCount();
        $closedIssueCount = $this->issueManager->getClosedIssueCount();

        return $this->render('Issue/issues.html.twig', [
            'issues' => $issues,
            'currentPage' => $page,
            'lastPage' => $lastPage ? $lastPage : $page,
            'openIssueCount' => $openIssueCount,
            'closedIssueCount' => $closedIssueCount
        ]);
    }

    /**
     * @Route("/issue/{repoOwner}/{repoName}/{number}", name="issue")
     * @param string $repoOwner
     * @param string $repoName
     * @param int $number
     * @return Response
     */
    public function issue(string $repoOwner, string $repoName, int $number): Response
    {
        $issue = $this->issueManager->getIssue($repoOwner, $repoName, $number);
        $comments = $this->issueManager->getComments($repoOwner, $repoName, $number);

        return $this->render('Issue/issue.html.twig', [
            'issue' => $issue,
            'comments' => $comments
        ]);
    }
}