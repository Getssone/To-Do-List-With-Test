<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'register')]
    public function register(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                // Utilisation de dd pour afficher les erreurs de validation
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('register');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPlainPassword($plainPassword);
            $user->setRoles();

            // Persist the new user
            $entityManager->persist($user);
            $entityManager->flush();

            // Authenticate the user
            //Ici 1er @param l'entité User pour dire qui sera "authentifier" 2nd @param $authenticatorName  qu'on retrouve dans security Yaml 3eme @param $firewallName pour indiquer sur quel firewall ont souhaite vérifier et configurer la connexion
            return $security->login($user, 'form_login', 'main');
        }

        // Ajoutez un message flash de test ici
        $this->addFlash('info', 'Formulaire chargé correctement');

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
