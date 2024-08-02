<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
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


    public function testEditUser(): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);
        $this->crawler = $this->client->request('GET', $this->getPath('edit.user', ['id' => $user->getId()]));
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Modifier' . ' ' . $user->getUsername());
        $form = $this->crawler->selectButton('Mise à jour')->form([
            'registration_form[username]' => 'UserEdit',
            'registration_form[plainPassword]' => 'UserEditPassword',
            'registration_form[roles]' => 'ROLE_ADMIN',
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects($this->getPath('list.user'));
        $this->client->followRedirect();

        $fixture = $em->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);

        $this->assertSame('UserEdit', $fixture->getUsername());
        $this->assertSame($user->getId(), $fixture->getId());
        $this->assertSame(['ROLE_USER', 'ROLE_ADMIN'], $fixture->getRoles());
    }

    public function testRemoveUser(): void
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository(User::class);
        $fixture = new User();
        $fixture->setEmail('Value');
        $fixture->setUsername('Value');
        $fixture->setPassword('Value');

        $em->persist($fixture);
        $em->flush();
        $this->crawler = $this->client->request('GET', $this->getPath('edit.user', ['id' => $fixture->getId()]));
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.btn.btn-danger.btn-sm.pull-right');
        $this->assertAnySelectorTextContains(".btn.btn-danger.btn-sm.pull-right", 'Supression');
        $buttonCrawlerNode = $this->crawler->selectButton('Supression');
        $form = $buttonCrawlerNode->form();
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(303);
        $em->clear();
        $foundUser = $repository->findOneBy(['id' => $fixture->getId()]);
        $this->assertNull($foundUser);
    }
}
