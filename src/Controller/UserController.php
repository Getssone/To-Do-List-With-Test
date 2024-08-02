<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class UserController extends AbstractController
{
    // #[Route('/', name: 'user')]
    // public function index(): Response
    // {
    //     return $this->render('user/user.html.twig', [
    //         'controller_name' => 'UserController',
    //     ]);
    // }
    #[Route('/list-user', name: 'list.user')]
    public function listTask(UserRepository $userRepos, Request $request): Response
    {

        $usersRequest = $userRepos->findAllDESC();

        return $this->render('pages/user/list.html.twig', [
            'users' => $usersRequest,
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/list-user-old', name: 'list.user.old')]
    public function listTaskOld(UserRepository $userRepos, Request $request): Response
    {

        $usersRequest = $userRepos->findAllDESC();

        return $this->render('pages/user/list-old.html.twig', [
            'users' => $usersRequest,
        ]);
    }

    #[Route('/{id}/edit-user', name: 'edit.user', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
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

            $entityManager->flush();

            $this->addFlash('success', 'User Modifié correctement');
            return $this->redirectToRoute('list.user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/user/edit.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/{id}/edit-user-old', name: 'edit.user.old', methods: ['GET', 'POST'])]
    public function editOld(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'User Modifié correctement');
            return $this->redirectToRoute('list.user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/user/edit-old.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/{id}/deleted-user', name: 'deleted.user', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list.user', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/{id}/deleted-user-old', name: 'deleted.user.old', methods: ['POST'])]
    public function deleteOld(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('list.user.old', [], Response::HTTP_SEE_OTHER);
    }
}
