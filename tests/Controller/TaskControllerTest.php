<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private  $client;
    private  $urlGenerator;
    private  $container;
    private UserRepository $userRepository;
    private TaskRepository $taskRepository;
    private $crawler;
    private $em;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->urlGenerator = $this->container->get('router');
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->crawler = $this->client->request('GET', $this->getPath('new.task'));
        $this->userRepository = $this->container->get(UserRepository::class);
        $this->taskRepository = $this->container->get(TaskRepository::class);
    }

    public function userConnected(): void
    {
        $client = $this->em->find(User::class, 1);
        $client->loginUser($client);
    }

    public function getPath($url): string
    {
        return  $this->urlGenerator->generate($url);
    }
    public function testConnectionPage(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains("Creation d'une tache");
    }
    public function testShowTaskForm()
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nouvelle Tache');
    }
    public function testSubmitRegisterFormWithInvalidDay()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Input "task_form[createdAt][day]" cannot take " " as a value (possible values: "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31").');

        $form = $this->crawler->selectButton('Ajouter')->form([
            'task_form[createdAt][day]' => ' ',
            'task_form[createdAt][month]' => ' ',
            'task_form[createdAt][year]' => ' ',
            'task_form[title]' => 'Title',
            'task_form[content]' => 'Content',
            'task_form[isDone]' => false
        ]);
    }
    public function testSubmitRegisterFormWithInvalidMonth()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Input "task_form[createdAt][month]" cannot take " " as a value (possible values: "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12").');

        $form = $this->crawler->selectButton('Ajouter')->form([
            'task_form[createdAt][day]' => "13",
            'task_form[createdAt][month]' => ' ',
            'task_form[createdAt][year]' => ' ',
            'task_form[title]' => 'Title',
            'task_form[content]' => 'Content',
            'task_form[isDone]' => false
        ]);
    }
    public function testSubmitRegisterFormWithInvalidYear()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Input "task_form[createdAt][year]" cannot take " " as a value (possible values: "2019", "2020", "2021", "2022", "2023", "2024", "2025", "2026", "2027", "2028", "2029").');

        $form = $this->crawler->selectButton('Ajouter')->form([
            'task_form[createdAt][day]' => "13",
            'task_form[createdAt][month]' => "7",
            'task_form[createdAt][year]' => ' ',
            'task_form[title]' => 'Title',
            'task_form[content]' => 'Content',
            'task_form[isDone]' => false
        ]);
    }

    public function testSubmitRegisterFormWithInvalidTitle()
    {
        $form = $this->crawler->selectButton('Ajouter')->form([
            'task_form[createdAt][day]' => "13",
            'task_form[createdAt][month]' => "7",
            'task_form[createdAt][year]' => "2024",
            'task_form[title]' => '',
            'task_form[content]' => 'Content',
            'task_form[isDone]' => false
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/new-task');

        $this->client->followRedirect();
    }
    public function testSubmitRegisterFormWithInvalidContent()
    {
        $form = $this->crawler->selectButton('Ajouter')->form([
            'task_form[createdAt][day]' => "13",
            'task_form[createdAt][month]' => "7",
            'task_form[createdAt][year]' => "2024",
            'task_form[title]' => 'Title',
            'task_form[content]' => '',
            'task_form[isDone]' => false
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/new-task');
        $this->client->followRedirect();
    }

    public function testSubmitRegisterFormWithValidData()
    {
        $form = $this->crawler->selectButton('Ajouter')->form([
            'task_form[createdAt][day]' => "13",
            'task_form[createdAt][month]' => "7",
            'task_form[createdAt][year]' => "2024",
            'task_form[title]' => 'Toto',
            "task_form[content]" => "Coco",
            'task_form[isDone]' => false
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects($this->getPath('list.task'));
        $this->client->followRedirect();
    }
}
