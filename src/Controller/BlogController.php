<?php

namespace App\Controller;

use App\Entity\BlogCategory;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
use Symfony\Component\Validator\Constraints\DateTime;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
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
     * @Route("/Blog/fullview/{id}", name="fullview")
     */
    public function fullview(ManagerRegistry $doctrine,$id):Response
    {
        $repository = $doctrine->getRepository(BlogArticle::class);
        $blogArticles = $repository->findBy(['id'=>$id]);


        return $this->render('Blog/fullview.html.twig', ['articles' => $blogArticles]);
    }
    /**
     * @Route("/Blog/fullviewC/{id}", name="fullviewC")
     */
    public function fullviewC(ManagerRegistry $doctrine,$id):Response
    {
        $repository = $doctrine->getRepository(BlogArticle::class);
        $blogArticles = $repository->findBy(['category'=>$id]);




        return $this->render('Blog/fullview.html.twig', ['articles' => $blogArticles]);
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
            ->add('created_at', DateTimeType::class)
            ->add('created_by', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Category!'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $manager = $doctrine->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash(
                'warning',
                'Category added successfully'
            );
            return $this->redirectToRoute('homepage');

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
            ->add('created_at', DateTimeType::class)
            ->add('created_by', TextType::class)
            ->add('category', EntityType::class, [
                'class' => BlogCategory::class,
                'choice_label' => 'name',])
            ->add('save', SubmitType::class, ['label' => 'Create Article!'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $manager = $doctrine->getManager();
            $manager->persist($article);
            $manager->flush();
            $this->addFlash(
                'warning',
                'Article added successfully'
            );
            return $this->redirectToRoute('homepage');
        }
        return $this->renderForm('Blog/newArt.html.twig', [
            'artForm' => $form,
        ]);

    }



}