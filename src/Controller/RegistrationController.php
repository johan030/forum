<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_registration')]
    public function create(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {
        # Création d'un utilisateur / client
        $user = new User();

        # Création du formulaire
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            # Encodage du mot de passe
            $clearPassword = $form->get('password')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $clearPassword));

            $user->setRoles(["ROLE_USER"]);
        
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            

            $this->addFlash('success', 'Votre compte a été créé avec succès ! Veuillez vous authentifier');
            return $this->redirectToRoute('app_login');
        }
        if ($form->isSubmitted() && $form->getErrors()) {
            $this->addFlash('warning', 'Vérifiez d\'avoir remplis tous les champs requis et d\'avoir accepté les conditions d\'utilisation de nos services');
        }
        
        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
