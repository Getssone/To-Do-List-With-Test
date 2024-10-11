<?php

namespace App\Tests;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class RegisterControllerTest extends WebTestCase
{
    private  $client;
    private  $urlGenerator;
    private  $container;
    private UserRepository $userRepository;
    private $crawler;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->urlGenerator = $this->container->get('router');
        $this->crawler = $this->client->request('GET', $this->getPath('register'));
        $this->userRepository = $this->container->get(UserRepository::class);
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

    public function testNoAdmin()
    {
        $this->assertResponseRedirects($this->getPath('homepage'));
    }
    public function testShowRegisterForm()
    {
        $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('register'));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Créer un utilisateur');
    }

    public function testSubmitRegisterFormWithInvalidData()
    {

        $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('register'));
        $form = $this->crawler->selectButton('Ajouter')->form([
            'registration_form[email]' => 'invalid-email',
            'registration_form[username]' => '',
            'registration_form[plainPassword]' => 'short',
            'registration_form[roles]' => 'ROLE_USER'
        ]);

        $this->client->submit($form);
        // Ensure that the response is a redirection to the same page (due to validation errors)
        $this->assertSelectorExists('.alert-danger');
    }

    public function testSaveNewUser(): void
    {

        $this->isConnected();
        $this->crawler = $this->client->request('GET', $this->getPath('register'));
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Créer un utilisateur');

        $form = $this->crawler->selectButton('Ajouter')->form([
            'registration_form[email]' => 'testToto@example.com',
            'registration_form[username]' => 'testToto',
            'registration_form[plainPassword]' => 'validpassword123',
            'registration_form[roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->getPath('homepage'));
        $this->client->followRedirect();



        $savedUser = $this->userRepository->findOneByEmail('testToto@example.com');

        $this->assertNotNull($savedUser, 'User should be saved in the database');
        $this->assertEquals('testToto', $savedUser->getUsername());
        $this->assertEquals(['ROLE_USER'], $savedUser->getRoles());
    }
}
