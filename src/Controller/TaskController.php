<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class TaskController extends AbstractController
{
    #[Route('/new-task', name: 'new.task')]
    public function createTask(Request $request, EntityManagerInterface $entityManager, #[CurrentUser()] ?User $user): Response
    {

        if (null === $user) {
            $this->addFlash('error', 'il faut être connecté pour pouvoir enregistrer une tâche');
            return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
        }

        $user;
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new DateTime());
            $task->setUser($user);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task Enregistré correctement');
            return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('pages/task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/new-task-old', name: 'new.task-old')]
    public function createTaskOld(Request $request, EntityManagerInterface $entityManager, #[CurrentUser()] ?User $user): Response
    {
        if (null === $user) {
            $this->addFlash('error', 'il faut être connecté pour pouvoir enregistrer une tâche');
            return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
        }

        $user;
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new DateTime());
            $task->setUser($user);
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'TaskOld Enregistré correctement');
            return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/task/create-old.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list-task', name: 'list.task')]
    public function listTask(TaskRepository $taskRepos, Request $request): Response
    {
        $requestURL = $request->query->get('q', 'all');
        if ($requestURL === 'done') {
            $tasksRequest = $taskRepos->findByStatus(true);;
        } elseif ($requestURL === 'notDone') {
            $tasksRequest = $taskRepos->findByStatus(false);;
        } else {
            $tasksRequest = $taskRepos->findAllDESC();
        }

        return $this->render('pages/task/list.html.twig', [
            'tasks' => $tasksRequest,
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/list-task-old', name: 'list.task-old')]
    public function listTaskOld(TaskRepository $taskRepos, Request $request): Response
    {
        $requestURL = $request->query->get('q', 'all');
        if ($requestURL === 'done') {
            $tasksRequest = $taskRepos->findByStatus(true);;
        } elseif ($requestURL === 'notDone') {
            $tasksRequest = $taskRepos->findByStatus(false);;
        } else {
            $tasksRequest = $taskRepos->findAllDESC();
        }

        return $this->render('pages/task/list-old.html.twig', [
            'tasks' => $tasksRequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit.task', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        $task->getCreatedAt();
        $task->setCreatedAt(new DateTime());

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task Modifié correctement');
            return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/task/edit.html.twig', [
            'task' => $task,
            'form' => $form
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/{id}/edit-old', name: 'edit.task-old', methods: ['GET', 'POST'])]
    public function editOld(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);


        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setCreatedAt(new DateTime());
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'TaskOld Modifié correctement');
            return $this->redirectToRoute('list.task-old', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/task/edit-old.html.twig', [
            'task' => $task,
            'form' => $form
        ]);
    }
    #[Route('/{id}/isDone', name: 'isDone.task', methods: ['POST'])]
    public function isDone(Request $request, Task $task, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('isDone' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $taskFound = $taskRepository->findBy(['id' => $task->getId()])[0];
            $IsWhat = $taskFound->getIsDone();
            $taskFound->setIsDone(!$IsWhat);
            $entityManager->persist($taskFound);
            $entityManager->flush();
            $this->addFlash('success', 'Task Modifié correctement');
            return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/{id}/isDone-old', name: 'isDone.task-old', methods: ['POST'])]
    public function isDoneOld(Request $request, Task $task, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('isDone' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $taskFound = $taskRepository->findBy(['id' => $task->getId()])[0];
            $IsWhat = $taskFound->getIsDone();
            $taskFound->setIsDone(!$IsWhat);
            $entityManager->persist($taskFound);
            $entityManager->flush();
            $this->addFlash('success', 'TaskOld Modifié correctement');
            return $this->redirectToRoute('list.task-old', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('list.task-old', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/deleted', name: 'deleted.task', methods: ['POST'])]
    public function delete(Request $request, #[CurrentUser()] ?User $user, Task $task, EntityManagerInterface $entityManager): Response
    {
        if (in_array("ROLE_ADMIN", $user->getRoles()) && $task->getUser()->getUsername() === "anonyme") {
            $this->addFlash('success', 'Task supprimé correctement');
            return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
        }
        if ($user !== $task->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas le créateur de cette tâches');
            return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task supprimé correctement');
            return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('list.task', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @codeCoverageIgnore
     */
    #[Route('/{id}/deleted-old', name: 'deleted.task-old', methods: ['POST'])]
    public function deleteOld(Request $request, #[CurrentUser()] ?User $user, Task $task, EntityManagerInterface $entityManager): Response
    {
        if (in_array("ROLE_ADMIN", $user->getRoles()) && $task->getUser()->getUsername() === "anonyme") {
            $this->addFlash('success', 'Task supprimé correctement');
            return $this->redirectToRoute('list.task-old', [], Response::HTTP_SEE_OTHER);
        }
        if ($user !== $task->getUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas le créateur de cette tâches');
            return $this->redirectToRoute('list.task-old', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();

            $this->addFlash('success', 'TaskOld supprimé correctement');
            return $this->redirectToRoute('list.task-old', [], Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('list.task-old', [], Response::HTTP_SEE_OTHER);
    }
}
