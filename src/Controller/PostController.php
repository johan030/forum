<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\AddCommentType;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/post{id}', name: 'show_post', methods:['GET', 'POST'])]
    public function showPost(Post $post, ManagerRegistry $doctrine, Request $request, CommentRepository $commentRepo): Response
    {
         /** @var User $user */
        $Userr = $this->getUser();

        $addComment = new Comment();
        #On stocke notre formulaire préparé dans notre variable
        $form = $this->createForm(AddCommentType::class, $addComment)->handleRequest($request);

        if ($Userr) {

            $addComment->setCreatedAt(new DateTime);
            $addComment->setStatus('published');
            $addComment->setUser($Userr);
            $addComment->setPost($post);

            if ($form->isSubmitted() && $form->isValid()) {
        
                $entityManager = $doctrine->getManager();
                $entityManager->persist($addComment);
                $entityManager->flush();

            } elseif ($form->isSubmitted() && $form->getErrors()) {
                
                $this->addFlash('warning', 'Erreur il manque des champs !');

            }
        }

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'comments' => $commentRepo->findByPostId($post->getId())
        ]);
    }
}
