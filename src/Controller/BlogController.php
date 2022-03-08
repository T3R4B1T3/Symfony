<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/")
     */

    public function index(): Response
    {

        return $this->render('Blog/index.html.twig', []);
    }
    /**
     * @Route("/Blog/list")
     */
    public function list(): Response
    {
        return $this->render('Blog/list.html.twig', []);
    }
    /**
     * @Route("/Blog/login")
     */
    public function login(): Response
    {
        return $this->render('Blog/login.html.twig', []);
    }
    /**
     * @Route("/Blog/about")
     */
    public function about(): Response
    {
        return $this->render('Blog/about.html.twig', []);
    }

}