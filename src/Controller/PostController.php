<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;

class PostController extends AbstractController
{
    #[Route('/post{id}', name: 'show_post', methods:['GET'])]
    public function showPost(Post $post): Response
    {
        return $this->render('post/index.html.twig', [
            'post' => $post
        ]);
    }
}
