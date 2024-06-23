<?php

namespace Tests\AppBundle\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerTest extends WebTestCase
{
    private  $client;
    private  $urlGenerator;
    private $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router');
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function crawlerGetPath($path)
    {
        return $this->client->request('GET', $this->urlGenerator->generate($path));
    }

    public function getImage($crawler, $alt)
    {
        return $crawler->selectImage($alt)->image();
    }  //function plus précise 
    public function getLinkID($crawler, $LinkId)
    {
        return $crawler->filter('#' . $LinkId)->link()->getUri();
    }  //function plus précise 
    public function getLinkClass($crawler, $LinkClass)
    {
        return $crawler->filter('.' . $LinkClass)->link()->getUri();
    }  //function plus précise 
    public function getLinkContent($crawler, $LinkContent)
    {
        return $crawler->filter($LinkContent)->link()->getUri();
    }  //function plus précise 

    public function testBasePage()
    {
        $crawler =  $this->crawlerGetPath('homepage');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $CDNBootstrap = $crawler->filter('html link')->matches('.CDNBootstrap');
        $this->assertTrue($CDNBootstrap);

        $this->assertPageTitleContains('Welcome To Do List !');

        $imageToDoList =  $this->getImage($crawler, 'todo list');
        $imageToDoListUri = $imageToDoList->getUri();
        $this->assertSame('http://localhost/assets/img/todolist_homepage-62c279f8a6263905d9b77ee896e03166.png', $imageToDoListUri);

        $imageGetssone =  $this->getImage($crawler, 'Created By Getssone');
        $imageGetssoneUri = $imageGetssone->getUri();
        $this->assertSame('http://localhost/assets/img/LogoCertifiedMedie-b8aceddfd3f317bb265058e376e04f07.svg', $imageGetssoneUri);

        $linkNavBar = $this->getLinkClass($crawler, 'navbar-brand');
        $this->assertSame('http://localhost/#', $linkNavBar);

        $linkUserCreate = $this->getLinkClass($crawler, 'userCreate');
        $this->assertSame('http://localhost/#', $linkUserCreate);

        $linkUserLogin = $this->getLinkClass($crawler, 'userLogin');
        $this->assertSame('http://localhost/#', $linkUserLogin);

        $linkUserLogout = $this->getLinkClass($crawler, 'userLogout');
        $this->assertSame('http://localhost/#', $linkUserLogout);


        $this->assertSame(2, $crawler->filterXPath('//*[contains(text(), "List")]')->count()); //  XPath sélectionne tous les éléments qui contiennent le texte "List" puis compte le nombre d'éléments correspondants.
        $this->assertSelectorTextContains('p', 'Copyright © Getssone');

        $CopyRight = $crawler->filter('html footer p ')->matches('p.pull-right');
        $this->assertTrue($CopyRight);
    }  //function plus précise 

    public function testHomePage()
    {
        $crawler =  $this->crawlerGetPath('homepage');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertPageTitleContains('Welcome To Do List !');

        $linkNewTasks = $this->getLinkClass($crawler, 'newTasks');
        $this->assertSame('http://localhost/#', $linkNewTasks);

        $linkToDoTasks = $this->getLinkClass($crawler, 'toDoTasks');
        $this->assertSame('http://localhost/#', $linkToDoTasks);

        $linkCompletedTasks = $this->getLinkClass($crawler, 'completedTasks');
        $this->assertSame('http://localhost/#', $linkCompletedTasks);
    }  //function plus précise 

    // Connexion
    public function testLinkConnexionIfLoggedOut()
    {
        $this->crawlerGetPath('homepage');
        $this->assertSelectorExists("a[href='/login']");
        $this->assertSelectorNotExists("a[href='/logout']");
    }

    // // Deconnexion
    public function testLinkToConnexionIfLoggedIn()
    {
        $testUser = $this->userRepository->findOneByEmail('test@example.com');

        // Vérifier que l'utilisateur n'est pas null
        $this->assertNotNull($testUser, 'L\'utilisateur doit être présent dans la base de données.');

        $this->client->loginUser($testUser);

        $this->crawlerGetPath('homepage');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists("a[href='/logout']");

        $this->assertSelectorNotExists("a[href='/login']");
    }
}
