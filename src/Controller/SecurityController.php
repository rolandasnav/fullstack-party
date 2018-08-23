<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function login()
    {
        return $this->get('oauth2.registry')->getClient('github')->redirect(['repo']);
    }

    /**
     * @Route("/login-check", name="login_check")
     */
    public function loginCheck()
    {
        return $this->render('Security/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }
}