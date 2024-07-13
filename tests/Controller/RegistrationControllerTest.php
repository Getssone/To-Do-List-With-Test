<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
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


    public function getPath($url): string
    {
        return  $this->urlGenerator->generate($url);
    }


    public function testShowRegisterForm()
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    public function testSubmitRegisterFormWithInvalidData()
    {
        $form = $this->crawler->selectButton('Inscription')->form([
            'registration_form[email]' => 'invalid-email',
            'registration_form[username]' => '',
            'registration_form[plainPassword]' => 'short'
        ]);

        $this->client->submit($form);

        // Ensure that the response is a redirection to the same page (due to validation errors)
        $this->assertResponseRedirects('/register');

        $this->client->followRedirect();

        $this->assertSelectorExists('.alert-danger');
    }

    public function testSaveNewUser(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Inscription');

        $form = $this->crawler->selectButton('Inscription')->form([
            'registration_form[email]' => 'testToto@example.com',
            'registration_form[username]' => 'testToto',
            'registration_form[plainPassword]' => 'validpassword123'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->getPath('homepage'));
        $this->client->followRedirect();



        $savedUser = $this->userRepository->findOneByEmail('testToto@example.com');
        $this->assertNotNull($savedUser, 'User should be saved in the database');
    }
}
