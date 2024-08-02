<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private  $client;
    private  $urlGenerator;
    private  $container;
    private $crawler;

    public function setUp(): void
    {

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->urlGenerator = $this->container->get('router');
        $this->crawler = $this->client->request('GET', $this->getPath('new.task'));
    }

    public function getPath(string $route, array $params = []): string
    {
        $url = $this->urlGenerator->generate($route, $params);
        return $url;
    }
    public function isConnected(): ?User
    {
        $userRepository = $this->container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@example.com');
        if ($testUser) {
            $this->client->loginUser($testUser);
            return $testUser;
        }
        return false;
    }
    public function testUserNotConnectedViaFlashBag(): void
    {
        $session = $this->client->getRequest()->getSession();
        $flashBag = $session->getFlashBag();
        $ArrayFlash = json_decode(json_encode($flashBag->get('error'))); //convertir le tableau "error" en une chaîne de caractères JSON. Puis re convertir une chaîne de caractères JSON en un objet PHP
        $this->assertCount(1, $ArrayFlash);
        $this->assertEquals('il faut être connecté pour pouvoir enregistrer une tâche', $ArrayFlash[0]);
    }

    public function testUserNotConnected(): void
    {

        $this->assertResponseRedirects($this->getPath('login'));;

        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger.alert-dismissible.fade.show .message-danger', 'il faut être connecté pour pouvoir enregistrer une tâche');
    }

    public function testConnectionPage(): void
    {

        $user = $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('new.task'));
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains("Creation d'une tache");
    }
    public function testShowTaskForm()
    {

        $user = $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('new.task'));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nouvelle Tache');
    }


    public function testSubmitRegisterFormWithInvalidTitle()
    {

        $user = $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('new.task'));
        $form = $this->crawler->selectButton('Ajouter')->form([
            // 'task_form[createdAt][day]' => "13",
            // 'task_form[createdAt][month]' => "7",
            // 'task_form[createdAt][year]' => "2024",
            'task_form[title]' => '',
            'task_form[content]' => 'Content',
            'task_form[isDone]' => false
        ]);
        $this->client->submit($form);
        $this->assertSelectorTextContains('ul li', 'Un titre est obligatoire');
    }
    public function testSubmitRegisterFormWithInvalidContent()
    {

        $user = $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('new.task'));
        $form = $this->crawler->selectButton('Ajouter')->form([
            // 'task_form[createdAt][day]' => "13",
            // 'task_form[createdAt][month]' => "7",
            // 'task_form[createdAt][year]' => "2024",
            'task_form[title]' => 'Title',
            'task_form[content]' => '',
            'task_form[isDone]' => false
        ]);
        $this->client->submit($form);
        $this->assertSelectorTextContains('ul li', 'Une information sur la tâche a faire est obligatoire');
    }

    public function testSubmitRegisterFormWithValidData()
    {

        $user = $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('new.task'));
        $form = $this->crawler->selectButton('Ajouter')->form([
            // 'task_form[createdAt][day]' => "13",
            // 'task_form[createdAt][month]' => "7",
            // 'task_form[createdAt][year]' => "2024",
            'task_form[title]' => 'Toto',
            'task_form[title]' => 'Toto',
            "task_form[content]" => "Coco",
            'task_form[isDone]' => false
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->getPath('list.task'));
        $this->client->followRedirect();
        $taskRepository = $this->container->get(TaskRepository::class);
        $lastTask = $taskRepository->findOneBy([], ['id' => 'DESC']);

        $this->assertSame($user->getId(), $lastTask->getUser()->getId());
    }

    public function testListWithQueryParameterDone()
    {
        $url = $this->getPath('list.task', ['q' => 'done']);

        // Requête pour la page de la liste des tâches avec le paramètre de requête
        $this->crawler = $this->client->request('GET', $url);

        // Vérification que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérification que le titre de la page est correct
        $this->assertPageTitleContains("Liste des tâches");

        // dd($this->crawler);
        // Vérification qu'il y a un message ou un élément indiquant qu'il n'y a pas de tâches
        $this->assertSelectorExists('.glyphicon.glyphicon-ok');
    }
    public function testListWithQueryParameterNotDone()
    {
        $url = $this->getPath('list.task', ['q' => 'notDone']);

        // Requête pour la page de la liste des tâches avec le paramètre de requête
        $this->crawler = $this->client->request('GET', $url);

        // Vérification que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérification que le titre de la page est correct
        $this->assertPageTitleContains("Liste des tâches");
        // dd($this->crawler);
        // Vérification qu'il y a un message ou un élément indiquant qu'il n'y a pas de tâches
        $this->assertSelectorExists('.glyphicon.glyphicon-remove');
    }

    public function testListWithoutData()
    {
        //Mock d'une Donnée ne fonctionne pas 
        // $taskRepositoryMock = $this->createMock(TaskRepository::class);

        // // Configure le mock pour renvoyer une liste vide
        // $taskRepositoryMock->expects(self::once())
        //     ->method('findAll')->willReturn([]);
        // // $this->client->enableReboot();
        // $this->container->set(TaskRepository::class, $taskRepositoryMock);
        // // Verify that the mock object is correctly set in the container
        // $this->assertSame(
        //     $taskRepositoryMock,
        //     $this->container->get(TaskRepository::class)
        // );
        //     $this->assertPageTitleContains("Liste des tâches");
        //     $this->assertSelectorTextContains('.alert.alert-warning', "Il n'y a pas encore de tâche enregistrée.");

        //DAMA\DoctrineTestBundle permet de ne pas enregistrer les actions sur la base de données ce qui nous permet de :
        // Nettoyer la BDD 
        $em = $this->container->get('doctrine')->getManager();
        $em->createQuery('DELETE FROM App\Entity\Task')->execute();
        $this->client->request('GET', $this->getPath('list.task'));
        $this->assertResponseIsSuccessful();

        $this->assertPageTitleContains("Liste des tâches");

        $this->assertSelectorTextContains('.alert.alert-warning', "Il n'y a pas encore de tâche enregistrée.");
    }

    public function testEditTaskWithInvalidContent()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $task = $entityManager->getRepository(Task::class)->findOneBy([], ['id' => 'ASC']);
        $this->crawler = $this->client->request('GET', $this->getPath('edit.task', ['id' => $task->getId()]));
        $form = $this->crawler->selectButton('Mise à jour')->form([
            'task_form[title]' => '',
        ]);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testEditTaskSuccessFull()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $task = $entityManager->getRepository(Task::class)->findOneBy([], ['id' => 'ASC']);
        $this->crawler = $this->client->request('GET', $this->getPath('edit.task', ['id' => $task->getId()]));
        $this->assertResponseIsSuccessful();
        $form = $this->crawler->selectButton('Mise à jour')->form([
            'task_form[title]' => 'TestUpdate',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->getPath('list.task'));
        $this->client->followRedirect();
    }
    public function testEditSimple()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $task = $entityManager->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);
        $this->crawler = $this->client->request('GET', $this->getPath('list.task', ['q' => 'done']));
        $this->assertResponseIsSuccessful();
        $buttonCrawlerNode =  $this->crawler->selectButton('Marquer non terminée');
        $this->assertCount(4, $buttonCrawlerNode);
        $form = $buttonCrawlerNode->form();
        $this->assertSame("return confirm('Êtes-vous sûr de vouloir modifier cet tâche ?');", $form->getFormNode()->getAttribute('onsubmit'));
        $this->assertSame('Marquer non terminée', $buttonCrawlerNode->text());
        $this->assertSame('/' . $task->getId() . '/isDone', $form->getFormNode()->getAttribute('action'));
        $this->client->submit($form);
        // Vérification après soumission
        // $this->assertResponseRedirects($this->getPath('list.task', ['q' => 'done']));
        $this->client->followRedirect();
        $this->client->getCrawler();
        $this->assertSelectorTextContains('.alert.alert-success', "Superbe ! Task Modifié correctement");
    }

    public function testDeleteTaskSuccessFull()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $repository = $entityManager->getRepository(Task::class);
        $task = $entityManager->getRepository(Task::class)->findOneBy([], ['id' => 'ASC']);
        $this->crawler = $this->client->request('GET', $this->getPath('edit.task', ['id' => $task->getId()]));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.btn.btn-danger.btn-sm.pull-right');
        $this->assertAnySelectorTextContains(".btn.btn-danger.btn-sm.pull-right", 'Supression');

        $buttonCrawlerNode = $this->crawler->selectButton('Supression');
        $form = $buttonCrawlerNode->form();
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(303);

        // Efface le cache de l'entity manager pour forcer la récupération des données à partir de la base de données
        $entityManager->clear();
        $foundTask = $repository->findOneBy(['id' => $task->getId()]);
        $this->assertNull($foundTask);
    }
    public function testDeletedSimple()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $task = $entityManager->getRepository(Task::class)->findOneBy([], ['id' => 'DESC']);
        $this->crawler = $this->client->request('GET', $this->getPath('list.task', ['q' => 'done']));
        $this->assertResponseIsSuccessful();
        $buttonCrawlerNode =  $this->crawler->selectButton('Sup. Tâche');
        $this->assertCount(4, $buttonCrawlerNode);
        $form = $buttonCrawlerNode->form();
        $this->assertSame("return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?');", $form->getFormNode()->getAttribute('onsubmit'));
        $this->assertSame('Sup. Tâche', $buttonCrawlerNode->text());
        $this->assertSame('/' . $task->getId() . '/deleted', $form->getFormNode()->getAttribute('action'));
        $this->client->submit($form);
        // Vérification après soumission
        $this->client->followRedirect();

        $this->client->getCrawler();
        $this->assertSelectorTextContains('.alert.alert-success', "Superbe ! Task Modifié correctement");
    }

    public function testAnonymeTask()
    {
        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $anonyme = $entityManager->getRepository(User::class)->findOneBy(['username' => 'anonyme']);
        $task = $entityManager->getRepository(Task::class)->findOneBy(['user' => $anonyme->getId()]);

        $this->assertNotNull($anonyme);
        $this->assertNotNull($task);
        $this->assertSame($task->getUser(), $anonyme);
    }
}
