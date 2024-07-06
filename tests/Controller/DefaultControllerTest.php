<?php

namespace Tests\AppBundle\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
        $this->userRepository = $container->get(UserRepository::class);
        $this->crawler = $this->client->request('GET', $this->urlGenerator->generate('homepage'));
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
    public function testFlashMessageInSession($flashType, $expectedMessage)
    {

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert.alert-success .message-success', 'Bienvenu dans votre Todo List');
    }



    public function testBasePage()
    {
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

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

        $linkUserCreate = $this->getLinkClass('register');
        $this->assertSame('http://localhost/register', $linkUserCreate);

        $linkUserLogin = $this->getLinkClass('userLogin');
        $this->assertSame('http://localhost/connexion', $linkUserLogin);

        $this->assertSame(3, $this->crawler->filterXPath('//*[contains(text(), "List")]')->count()); //  XPath sélectionne tous les éléments qui contiennent le texte "List" puis compte le nombre d'éléments correspondants.

        $CopyRight = $this->crawler->filter('html footer p ')->matches('p.pull-right');
        $this->assertTrue($CopyRight);
    }

    public function testHomePage()
    {
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertPageTitleContains('Welcome To Do List !');

        $linkNewTasks = $this->getLinkClass('newTasks');
        $this->assertSame('http://localhost/#', $linkNewTasks);

        $linkToDoTasks = $this->getLinkClass('toDoTasks');
        $this->assertSame('http://localhost/#', $linkToDoTasks);

        $linkCompletedTasks = $this->getLinkClass('completedTasks');
        $this->assertSame('http://localhost/#', $linkCompletedTasks);
    }

    // Connexion
    public function testLinkConnexionIfLoggedOut()
    {

        $this->assertSelectorExists("a[href='/register']");
        $this->assertSelectorExists("a[href='/connexion']");
        $this->assertSelectorNotExists("a[href='/logout']");
    }

    // // Deconnexion
    public function testLinkToConnexionIfLoggedIn()
    {
        $testUser = $this->userRepository->findOneByEmail('test@example.com');

        // Vérifier que l'utilisateur n'est pas null
        $this->assertNotNull($testUser, 'L\'utilisateur doit être présent dans la base de données.');

        $this->client->User($testUser);

        $this->assertResponseIsSuccessful();

        //Ici on relance la page pour effectuer la connexion
        $this->client->request('GET', $this->urlGenerator->generate('homepage'));

        $this->assertSelectorExists("a[href='/logout']");
        $this->assertSelectorNotExists("a[href='/connexion']");
        $btnCreateProfil = $this->crawler->filter('.register.btn.btn-outline-secondary')->text();
        $this->assertEquals("Créer un utilisateur", $btnCreateProfil);
        $btnConnexion = $this->crawler->filterXPath('//body/nav/div/div/a')->eq(2)->text(); //test via le traversing
        $this->assertEquals("Se connecter", $btnConnexion);
    }
}
