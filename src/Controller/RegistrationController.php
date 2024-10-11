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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'register')]
    // #[IsGranted('ROLE_ADMIN', message: "Vous n'avez pas les droits suffisants pour créer un utilisateur")] // intéressant à utiliser mais ne peut apporter le message dans les addFlashs
    public function register(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les droits suffisants pour créer un utilisateur');
            return $this->redirectToRoute('homepage');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPlainPassword($plainPassword);
            $role = $form->get('roles')->getData();
            if ($role === 'ROLE_USER') {
                $user->setRoles();
            } elseif ($role === 'ROLE_ADMIN') {
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            };

            // Persist the new user
            $entityManager->persist($user);
            $entityManager->flush();

            // Authenticate the user
            //Ici 1er @param l'entité User pour dire qui sera "authentifier" 2nd @param $authenticatorName  qu'on retrouve dans security Yaml 3eme @param $firewallName pour indiquer sur quel firewall ont souhaite vérifier et configurer la connexion
            return $security->login($user, 'form_login', 'main');
        }

        // Ajoutez un message flash de test ici
        $this->addFlash('success', 'Formulaire chargé correctement');

        return $this->render('pages/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/register-old', name: 'register-old')]
    public function registerOld(Request $request, Security $security, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les droits suffisants pour créer un utilisateur');
            return $this->redirectToRoute('login-old');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                // Utilisation de dd pour afficher les erreurs de validation
                $this->addFlash('error', $error->getMessage());
            }
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
        $this->addFlash('success', 'Formulaire chargé correctement');

        return $this->render('pages/registration/register-old.html.twig', [
            'form' => $form,
        ]);
    }
}
