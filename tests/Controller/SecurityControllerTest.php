<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
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
        $this->crawler = $this->client->request('GET', $this->getPath('login'));
    }


    public function getPath($url): string
    {
        return  $this->urlGenerator->generate($url);
    }

    public function testShowLoginForm()
    {
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Connexion');
    }

    public function testLoginWithBadEmail(): void
    {
        $this->assertResponseIsSuccessful();

        $form = $this->crawler->filter('form[name=login]')->form([
            '_username' => 'doesNotExist',
            '_password' => 'validpassword123',
        ]);

        $this->client->submit($form);

        //Mise a jour du visuel Crawler
        $this->crawler = $this->client->getCrawler();
        $this->assertResponseRedirects('/login', 302);
        $this->client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger', 'Identifiants invalides.');
    }
    public function testLoginWithBadPassword(): void
    {
        $this->assertResponseIsSuccessful();

        $form = $this->crawler->selectButton('Connexion')->form([
            '_username' => 'test@example.com',
            '_password' => 'w',
        ]);

        $this->client->submit($form);

        //Mise a jour du visuel Crawler
        $this->crawler = $this->client->getCrawler();
        $this->assertResponseRedirects('/login', 302);
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger', 'Identifiants invalides.');
    }

    public function testLoginValid(): void
    {
        // Assurez-vous que la page de connexion est chargée
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Connexion');

        $form = $this->crawler->selectButton('Connexion')->form([
            '_username' => 'test@example.com',
            '_password' => 'validpassword123'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects($this->getPath('homepage'), Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertRouteSame('homepage');
        $this->assertResponseIsSuccessful();
    }
}
