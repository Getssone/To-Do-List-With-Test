<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerTest extends WebTestCase
{
    private  $client;
    private  $urlGenerator;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router');
    }

    public function crawlerPath($path)
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

    public function testBase()
    {
        $crawler =  $this->crawlerPath('homepage');
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

        $this->assertSame(2, $crawler->filterXPath('//*[contains(text(), "List")]')->count()); //  XPath sélectionne tous les éléments qui contiennent le texte "List" puis compte le nombre d'éléments correspondants.
        $this->assertSelectorTextContains('p', 'Copyright © Getssone');

        $CopyRight = $crawler->filter('html footer p ')->matches('p.pull-right');
        $this->assertTrue($CopyRight);
    }  //function plus précise 

    public function testHomePage()
    {
        $crawler =  $this->crawlerPath('homepage');
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
    // public function testExistLinkToConnexionIfLoggedOut()
    // {
    //     $this->assertSelectorExists("a[href='/login']");
    // }

    // public function testNotExistLinkToConnexionIfLoggedIn()
    // {
    //     $loggedClient = self::createClient([], [
    //         'PHP_AUTH_USER' => 'gets@mail.fr',
    //         'PHP_AUTH_PW' => 'gets'
    //     ]);

    //     $loggedClient->request("GET", "/");

    //     $this->assertSelectorNotExists("a[href='/login']");
    // }

    // // Deconnexion
    // public function testExistLinkToDeconnexionIfLoggedIn()
    // {
    //     $loggedClient = self::createClient([], [
    //         'PHP_AUTH_USER' => 'gets@mail.fr',
    //         'PHP_AUTH_PW' => 'gets'
    //     ]);

    //     $loggedClient->request("GET", "/");

    //     $this->assertSelectorExists("a[href='/logout']");
    // }

    // public function testNotExistsLinkToDeconnexionIfLoggedOut()
    // {
    //     $client = self::createClient();

    //     $client->request("GET", "/");

    //     $this->assertSelectorNotExists("a[href='/logout']");
    // }


    // public function testHomePageGeneral()
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/');

    //     $this->assertResponseIsSuccessful(); //Vérifie que la réponse HTTP est une réponse réussie (code de statut dans la plage 200-299).

    //     $this->assertSelectorTextContains('h1', 'DefaultController');
    // } //function plus général 
}
