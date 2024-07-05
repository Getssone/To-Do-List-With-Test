<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();
        $errorMessage = 'Le nom du profil ou le mot de passe est incorrect. Veuillez réessayer.';
        if ($error) {
            // Vous pouvez également logger l'erreur ici si nécessaire
        }
        $this->addFlash('error', $errorMessage);

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $lastUsername
        ]);
    }

    #[Route('/deconnexion', name: 'security.logout', methods: ['GET'])]
    public function logout(): void
    {
        // Nothing to do here...
    }
}

    // public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    // {
    //     // get the login error if there is one
    //     $error = $authenticationUtils->getLastAuthenticationError();

    //     // last username entered by the user
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     $user = new User();
    //     $user->setEmail($lastUsername);

    //     $form = $this->createForm(LoginFormType::class, $user);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted()) {
    //         if ($form->isValid()) {
    //             $this->addFlash('success', 'Bienvenu dans votre Todo List');
    //             return $this->render('default/index.html.twig', ['controller_name' => 'HomePage',]);
    //         } else {
    //             $this->addFlash('error', 'Une erreur doit être présente dans le formulaire.');
    //         }
    //     }
    //     return $this->render('security/index.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    // /**
    //  * @Route("/login", name="login")
    //  */
    // public function loginAction(Request $request)
    // {
    //     $authenticationUtils = $this->get('security.authentication_utils');

    //     $error = $authenticationUtils->getLastAuthenticationError();
    //     $lastUsername = $authenticationUtils->getLastUsername();

    //     return $this->render('security/login.html.twig', array(
    //         'last_username' => $lastUsername,
    //         'error'         => $error,
    //     ));
    // }

    // /**
    //  * @Route("/login_check", name="login_check")
    //  */
    // public function loginCheck()
    // {
    //     // This code is never executed.
    // }

    // /**
    //  * @Route("/logout", name="logout")
    //  */
    // public function logoutCheck()
    // {
    //     // This code is never executed.
    // }
