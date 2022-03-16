<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Form\AddPostType;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(PostRepository $posts, Request $request, ManagerRegistry $doctrine): Response
    {
        /** @var User $user */
        $Userr = $this->getUser();

        $addpost = new Post();

        #On stocke notre formulaire préparé dans notre variable
        $form = $this->createForm(AddPostType::class, $addpost)->handleRequest($request);

        if ($Userr) {

            $addpost->setCreatedAt(new DateTime);
            $addpost->setStatus('published');
            $addpost->setUser($Userr);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $doctrine->getManager();
                $entityManager->persist($addpost);
                $entityManager->flush();
            } elseif ($form->isSubmitted() && $form->getErrors()) {
                $this->addFlash('warning', 'Post Envoyé !');
            }
        }
        elseif($form->isSubmitted() && $form->getErrors())
        {
            $this->addFlash('warning', 'Post Envoyé !');
        }


        return $this->render('home/index.html.twig', [
            'posts' => $posts->getPostsOrderBy(),
            'form' => $form->createView()
        ]);
    }
}
