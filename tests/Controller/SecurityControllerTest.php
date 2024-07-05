<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
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
        $this->crawler = $this->client->request('GET', $this->urlGenerator->generate('login'));
    }

    // Connexion
    // public function testLinkConnexionIfLoggedOut()
    // {
    //     $this->assertSelectorExists("a[href='/register']");
    //     $this->assertSelectorNotExists("a[href='/login']");
    //     $this->assertSelectorNotExists("a[href='/logout']");
    // }
    // public function testIfLoginIsSuccessfull(): void
    // {
    //     $form = $this->crawler->filter("form[name=login]")->form([
    //         "email" => 'test@example.com',
    //         "password" => 'validpassword123'
    //     ]);
    //     $this->client->submit($form);
    //     $this->assertResponseIsSuccessful();
    //     $this->assertRouteSame('homepage');
    //     // $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    //     // // Vérifiez que la soumission du formulaire a redirigé l'utilisateur vers la page attendue
    // }

    public function testIfLoginIsSuccessfullWithForm(): void
    {
        $form = $this->crawler->filter("form[name=login_form]")->form([
            "login_form[email]" => 'test@example.com',
            "login_form[password]" => 'validpassword123'
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        dd($this->crawler);
        $this->assertRouteSame('homepage');
    }
    // public function testIfLoginFailedWhenPasswordIsWrong(): void
    // {
    //     $form = $this->crawler->filter("form[name=login]")->form([
    //         "_username" => '@example',
    //         "_password" => 'tt'
    //     ]);
    //     $this->client->submit($form);
    //     $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    //     $this->assertRouteSame('login');
    //     $this->assertSelectorTextContains('div.alert.alert-danger.alert-dismissible.fade.show', 'Oops!');
    // }
}
