<?php

namespace App\Controller;

use App\{Entity\BlogArticle,
    Entity\BlogCategory,
    Entity\Comment,
    Entity\User,
    Repository\CommentRepository,
    Repository\UserRepository
};
use DateTimeImmutable;
use Doctrine\{ORM\EntityManagerInterface,
    ORM\EntityRepository,
    Persistence\ManagerRegistry,
    Persistence\ObjectRepository
};
use Exception;
use Symfony\{Bridge\Doctrine\Form\Type\EntityType,
    Bundle\FrameworkBundle\Controller\AbstractController,
    Component\Form\Extension\Core\Type\DateTimeType,
    Component\Form\Extension\Core\Type\DateType,
    Component\Form\Extension\Core\Type\SubmitType,
    Component\Form\Extension\Core\Type\TextareaType,
    Component\Form\Extension\Core\Type\TextType,
    Component\HttpFoundation\RedirectResponse,
    Component\HttpFoundation\Request,
    Component\HttpFoundation\Response,
    Component\Routing\Annotation\Route,
    Component\Security\Core\Security,
    Component\Form\FormTypeInterface,
};
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Stmt\Else_;

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
     * @Route("/Blog/Articles" ,name ="Articles")
     */
    public function article(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(BlogArticle::class);
        $blogArticles = $repository->findAll();

        return $this->render('Blog/Articles.html.twig', ['articles' => $blogArticles]);
    }

    /**
     * @Route("/Blog/Categories", name="Categories")
     */
    public function category(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(BlogCategory::class);
        $blogCategories = $repository->findAll();

        return $this->render('Blog/Categories.html.twig', ['categories' => $blogCategories]);
    }

    /**
     * @Route("/Blog/fullviewArticle/{id}", name="fullview")
     */
    public function fullviewArticle(ManagerRegistry $doctrine, $id, Request $request): Response
    {
        $repository = $doctrine->getRepository(BlogArticle::class);
        $blogArticles = $repository->findBy(['id' => $id]);
        $blogArticlesOne = $repository->findOneBy(['id' => $id]);
        $comment = new Comment();
        $repositoryComment = $doctrine->getRepository(Comment::class);
        $comments = $repositoryComment->findBy(['Article' => $id]);

        $form = $this->createFormBuilder($comment)
            ->add('text', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Post Comment!'])
            ->getForm();

        $comment->setUser($this->getUser());
        $comment->setCreatedAt(new DateTimeImmutable());
        $comment->setArticle($blogArticlesOne);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $manager = $doctrine->getManager();
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'warning',
                'Comment added successfully'
            );
            return $this->redirectToRoute("Articles");
        }
        return $this->renderForm('Blog/fullview.html.twig', [
            'commentForm' => $form,
            'articles' => $blogArticles,
            'commentView' => $comments,
        ]);
    }

    /**
     * @Route("/Blog/fullviewCategory/{id}", name="fullviewC")
     */
    public function fullviewCategory(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(BlogArticle::class);
        $blogArticles = $repository->findBy(['category' => $id]);

        return $this->render('Blog/fullviewC.html.twig', ['articles' => $blogArticles]);
    }

    /**
     * @Route("/Blog/newCategory", name="newCategory")
     */
    public function newCategory(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new BlogCategory();

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('created_by', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Category!'])
            ->getForm();

        $category->setCreatedAt(new DateTimeImmutable());

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
     * @Route("/Blog/newArticle", name="newArticle")
     */

    public function newArticle(Request $request, ManagerRegistry $doctrine): Response
    {
        $article = new BlogArticle();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class)
            ->add('descritpion', TextType::class)
            ->add('article', TextareaType::class)
            ->add('created_by', TextType::class)
            ->add('category', EntityType::class, [
                'class' => BlogCategory::class,
                'choice_label' => 'name',
            ])
            ->add('save', SubmitType::class, ['label' => 'Create Article!'])
            ->getForm();

        $article->setCreatedAt(new DateTimeImmutable());

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
            return $this->redirectToRoute('Articles');
        }
        return $this->renderForm('Blog/newArt.html.twig', [
            'artForm' => $form,
        ]);
    }

    /**
     * @Route("/Blog/deleteCommment/{id}/{username}/{currentuser}", name="delete_comment")
     */

    public function deleteComment($id,$username,$currentuser ,ManagerRegistry $doctrine): Response
    {

        $userRep = $doctrine->getRepository(User::class);
        $user = $userRep->findOneBy(array('username' => $currentuser));
        $zmienna =  $user->getRoles();


        if ($currentuser == $username || in_array("ROLE_ADMIN",$zmienna)) {
            $repository = $doctrine->getRepository(Comment::class);
            $comment = $repository->find($id);
            $manager = $doctrine->getManager();
            $manager->remove($comment);
            $manager->flush();

            $this->addFlash(
                'warning',
                'Comment deleted successfully'
            );
        }else{
            $this->addFlash(
                'warning',
                'You can not delete not your comments'
            );
        }
        return $this->redirectToRoute("Articles");
    }

    /**
     * @Route("/Blog/editComment/{id}/{username}/{currentuser}", name ="edit_comment")
     */

    public function editComment($id,$username,$currentuser,ManagerRegistry $doctrine,Request $request):Response
    {
        $repository = $doctrine->getRepository(Comment::class);
        $comment = $repository->findOneBy(['id' => $id]);

        $userRep = $doctrine->getRepository(User::class);
        $user = $userRep->findOneBy(array('username' => $currentuser));
        $zmienna =  $user->getRoles();


        $form = $this->createFormBuilder($comment)
            ->add('text', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Post Comment!'])
            ->getForm();

        $text =$comment->getText();


            $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($currentuser == $username || in_array("ROLE_ADMIN",$zmienna)) {
            $manager = $doctrine->getManager();
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'warning',
                'Comment edited successfully'
            );
        }else{
            $this->addFlash(
                'warning',
                'You can not edit not your comments'
            );
        }
           return $this->redirectToRoute("Articles");
       }
        return $this->renderForm('Blog/commentEditForm.html.twig', [
            'commEditForm' => $form,
            'Text' => $text
        ]);
    }

}