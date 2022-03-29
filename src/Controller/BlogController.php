<?php

namespace App\Controller;

use App\Entity\BlogCategory;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BlogArticle;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;

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
    /**
     * @Route("/Blog/Articles")
     */
    public function article(ManagerRegistry $doctrine):Response
    {
        $repository = $doctrine->getRepository(BlogArticle::class);
        $blogArticles = $repository->findAll();

        return $this->render('Blog/Articles.html.twig', ['articles' => $blogArticles]);
    }
    /**
     * @Route("/Blog/Categories")
     */
    public function category(ManagerRegistry $doctrine):Response
    {
        $repository = $doctrine->getRepository(BlogCategory::class);
        $blogCategories = $repository->findAll();

        return $this->render('Blog/Categories.html.twig', ['categories' => $blogCategories]);
    }
    /**
     * @Route("/Blog/formC")
   */
    public function newCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new BlogCategory();

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('created_at', DateType::class)
            ->add('created_by', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Category!'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $manager = $doctrine->getManager();
            $manager->persist($category);
            $manager->flush();

            echo "dodano pomyslnie";
        }
        return $this->renderForm('Blog/newCat.html.twig', [
            'catForm' => $form,
        ]);

    }
    /**
     * @Route("/Blog/formA")
     */
    public function newArticle(Request $request, ManagerRegistry $doctrine): Response
    {
        $article = new BlogArticle();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('descritpion', TextType::class)
            ->add('article', TextareaType::class)
            ->add('created_at', DateType::class)
            ->add('created_by', TextType::class)
            ->add('category', BlogCategory::class)
            ->add('save', SubmitType::class, ['label' => 'Create Article!'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $manager = $doctrine->getManager();
            $manager->persist($article);
            $manager->flush();

            echo "dodano pomyslnie";
        }
        return $this->renderForm('Blog/newArt.html.twig', [
            'artForm' => $form,
        ]);

    }

}