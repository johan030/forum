<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Form\AddPostType;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods:['GET', 'POST'])]
    public function index(PostRepository $posts, Request $request): Response
    {
        $addpost = new Post();
        $addpost->setCreatedAt(new DateTime);
        $addpost->setStatus('published');

        #On stocke notre formulaire préparé dans notre variable
        $form = $this->createForm(AddPostType::class, $addpost)->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($addpost);
            $entityManager->flush();
        }

        return $this->render('home/index.html.twig', [
            'posts' => $posts->getPostsOrderBy(),
            'form' => $form->createView() 
        ]);
    }
}
