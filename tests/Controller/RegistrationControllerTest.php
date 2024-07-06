<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{

    private  $client;
    private  $urlGenerator;
    private  $container;
    // private $userRepository;
    private $crawler;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->urlGenerator = $this->container->get('router');
        // $this->userRepository = $this->container->get(UserRepository::class);
        $this->crawler = $this->client->request('GET', $this->getPath('register'));
    }

    public function getPath($url): string
    {
        return  $this->urlGenerator->generate($url);
    }


    public function testShowRegisterForm()
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Register');
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

    public function testSubmitRegisterFormWithValidData()
    {
        $form = $this->crawler->selectButton('Inscription')->form([
            'registration_form[email]' => 'testy@example.com',
            'registration_form[username]' => 'testyuser',
            'registration_form[plainPassword]' => 'validpassword123'
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects($this->getPath('homepage'));

        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success .message-success', 'Bienvenu dans votre Todo List');
    }
}
