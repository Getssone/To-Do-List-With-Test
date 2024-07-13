<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/new-task', name: 'new.task')]
    public function createTask(Request $request, EntityManagerInterface $entityManager): Response
    {

        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('new.task');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the new task
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Formulaire chargé correctement');
            return $this->redirectToRoute('list.task');
        }
        return $this->render('pages/task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new-task-old', name: 'new.task-old')]
    public function createTaskOld(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('new.task-old');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the new task
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Formulaire chargé correctement');
            return $this->redirectToRoute('list-task-old');
        }

        // Ajoutez un message flash de test ici
        $this->addFlash('success', 'Formulaire chargé correctement');
        return $this->render('pages/task/create-old.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list-task', name: 'list.task')]
    public function listTask(TaskRepository $taskRepos): Response
    {
        $tasksRequest = $taskRepos->findAll();

        return $this->render('pages/task/list.html.twig', [
            'tasks' => $tasksRequest,
        ]);
    }
    #[Route('/list-task-old', name: 'list.task-old')]
    public function listTaskOld(TaskRepository $taskRepos): Response
    {
        $tasksRequest = $taskRepos->findAll();
        $this->addFlash('success', 'Formulaire chargé correctement');
        return $this->render('pages/task/list-old.html.twig', [
            'tasks' => $tasksRequest,
        ]);
    }
}
