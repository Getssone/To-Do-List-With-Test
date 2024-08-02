<?php

namespace Tests\AppBundle\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

require __DIR__ . '/../../vendor/autoload.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Notifier\FlashMessage\BootstrapFlashMessageImportanceMapper;
use Symfony\Component\Notifier\Message\MessageInterface;

class DefaultControllerTest extends WebTestCase
{
    private  $client;
    private  $urlGenerator;
    private $userRepository;
    private $crawler;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->urlGenerator = $container->get('router');
        $this->crawler = $this->client->request('GET', $this->urlGenerator->generate('homepage'));
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function profileConnected()
    {
        $testUser = $this->userRepository->findOneByEmail('test@example.com');

        // Vérifier que l'utilisateur n'est pas null
        $this->assertNotNull($testUser, 'L\'utilisateur doit être présent dans la base de données.');

        $this->client->loginUser($testUser);
        //Ici on relance la page pour effectuer la connexion
        $this->client->request('GET', $this->urlGenerator->generate('homepage'));
    }
    public function getImage($alt)
    {
        return $this->crawler->selectImage($alt)->image();
    }
    public function getLinkID($LinkId)
    {
        return $this->crawler->filter('#' . $LinkId)->link()->getUri();
    }
    public function getLinkClass($LinkClass)
    {
        return $this->crawler->filter('.' . $LinkClass)->link()->getUri();
    }
    public function getLinkContent($LinkContent)
    {
        return $this->crawler->filter($LinkContent)->link()->getUri();
    }
    public function flashMessageProvider()
    {
        return [
            ['success', 'You made it'],
            ['error', 'Oops! Something went wrong'],
        ];
    }
    /**
     * @dataProvider flashMessageProvider
     */
    public function testFlashMessageInPage($flashType, $expectedMessage)
    {
        $container = $this->client->getContainer();

        // Creation d'un FlashBag Mocker
        $flashBag = new FlashBag();
        $flashBag->add($flashType, $expectedMessage);

        // Creation d'une session Mocker
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $session->registerBag($flashBag);
        // Intégration des service session mocker dans le container
        $container->set('session', $session);
        $this->assertSame([$expectedMessage], $this->client->getContainer()->get('session')->getFlashBag()->peek($flashType));
    }
    /**
     * @dataProvider flashMessageProvider
     */
    public function testFlashMessageInSession()
    {
        $this->profileConnected();
        //Mise a jour du visuel Crawler
        $this->crawler = $this->client->getCrawler();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert.alert-success.alert-dismissible.fade.show .message-success', 'Bienvenu dans votre Todo List');
    }



    public function testBasePage()
    {

        $this->profileConnected();
        //Mise a jour du visuel Crawler
        $this->crawler = $this->client->getCrawler();
        $this->assertResponseIsSuccessful();

        $CDNBootstrap = $this->crawler->filter('html link')->matches('.CDNBootstrap');
        $this->assertTrue($CDNBootstrap);

        $this->assertPageTitleContains('Welcome To Do List !');

        $imageToDoList =  $this->getImage('todo list');
        $imageToDoListUri = $imageToDoList->getUri();
        $this->assertSame('http://localhost/assets/img/todolist_homepage-62c279f8a6263905d9b77ee896e03166.png', $imageToDoListUri);

        $imageGetssone =  $this->getImage('Created By Getssone');
        $imageGetssoneUri = $imageGetssone->getUri();
        $this->assertSame('http://localhost/assets/img/LogoCertifiedMedie-b8aceddfd3f317bb265058e376e04f07.svg', $imageGetssoneUri);

        $linkNavBar = $this->getLinkClass('navbar-brand');
        $this->assertSame('http://localhost/#', $linkNavBar);

        $linkUserLogout = $this->getLinkClass('userLogout');
        $this->assertSame('http://localhost/logout', $linkUserLogout);

        $this->assertSame(3, $this->crawler->filterXPath('//*[contains(text(), "List")]')->count()); //  XPath sélectionne tous les éléments qui contiennent le texte "List" puis compte le nombre d'éléments correspondants.

        $CopyRight = $this->crawler->filter('html footer p ')->matches('p.pull-right');
        $this->assertTrue($CopyRight);
    }

    public function testHomePage()
    {

        $this->profileConnected();
        //Mise a jour du visuel Crawler
        $this->crawler = $this->client->getCrawler();
        $this->assertResponseIsSuccessful();

        $this->assertPageTitleContains('Welcome To Do List !');

        $linkNewTasks = $this->getLinkClass('newTasks');
        $this->assertSame('http://localhost/new-task', $linkNewTasks);

        $linkToDoTasks = $this->getLinkClass('toDoTasks');
        $this->assertSame('http://localhost/list-task?q=notDone', $linkToDoTasks);

        $linkCompletedTasks = $this->getLinkClass('completedTasks');
        $this->assertSame('http://localhost/list-task?q=done', $linkCompletedTasks);
    }

    // Déconnecté
    public function testLinkToConnexionIfLoggedOut()
    {
        $this->assertResponseRedirects('/login', 302);
        $this->client->followRedirect();
    }

    // Connecté
    public function testLinkToConnexionIfLoggedIn()
    {
        $testUser = $this->userRepository->findOneByEmail('test@example.com');

        // Vérifier que l'utilisateur n'est pas null
        $this->assertNotNull($testUser, 'L\'utilisateur doit être présent dans la base de données.');

        $this->client->loginUser($testUser);
        //Ici on relance la page pour effectuer la connexion
        $this->client->request('GET', $this->urlGenerator->generate('homepage'));
        $this->assertResponseIsSuccessful();
        $loggedUser = $this->client->getContainer()->get('security.token_storage')->getToken()->getUser();
        $this->assertEquals($testUser->getEmail(), $loggedUser->getEmail(), "L\'utilisateur connecté doit être le même que celui utilisé pour la connexion.");

        $this->assertSelectorExists("a[href='/logout']");
        $this->assertSelectorNotExists("a[href='/login']");
        $this->assertSelectorNotExists("a[href='/register']");
        $this->assertSelectorTextContains(".userLogout.pull-right.btn-outline-secondary", "Se déconnecter");
    }
}
