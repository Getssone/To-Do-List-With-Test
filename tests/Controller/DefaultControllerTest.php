<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DefaultControllerTest extends WebTestCase
{
    private  $client;
    private  $urlGenerator;

    public function setUp(): void
    {
        // parent::setUp();
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router'); // Les tests seront plus robustes contre les changements de routes, car ils utilisent les noms des routes définis dans le routeur de Symfony.
    }

    public function testHomePage2()
    {
        $crawler =  $this->client->request('GET', $this->urlGenerator->generate('homepage'));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame(2, $crawler->filterXPath('//*[contains(text(), "List")]')->count()); //  XPath sélectionne tous les éléments qui contiennent le texte "你好" puis compte le nombre d'éléments correspondants.
        // $this->assertSelectorTextContains('h1', 'List');
    }  //function plus précise 

    // public function testHomePage()
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/');

    //     $this->assertResponseIsSuccessful(); //Vérifie que la réponse HTTP est une réponse réussie (code de statut dans la plage 200-299).

    //     $this->assertSelectorTextContains('h1', 'DefaultController');
    // } //function plus général 
}
