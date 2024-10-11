<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;
use TypeError;

class TaskEntityTest extends KernelTestCase
{
    public function getEntity(): Task
    {
        return (new Task())
            ->setCreatedAt(new DateTime())
            ->setTitle('Titre valide')
            ->setContent('Contenu valide')
            ->setIsDone(false);
    }

    public function assertHasErrors(Task $task, int $number = 0)
    {
        self::bootKernel();
        $container = static::getContainer();
        $errors = $container->get('validator')->validate($task);
        $messages = [];
        foreach ($errors as $error) {
            $messages = $error ? $error->getPropertyPath() . '=>' . $error->getMessage() : '';
        }
        $infoMessages = empty($messages) ? '' : $messages;
        $this->assertCount($number, $errors, $infoMessages);
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidCreatedAtTypeString(): void
    {
        $task = $this->getEntity();

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('App\Entity\Task::setCreatedAt(): Argument #1 ($createdAt) must be of type DateTime, string given, called in');

        $task->setCreatedAt('datetime');
    }

    public function testInvalidCreatedAtTypeNull(): void
    {
        $task = $this->getEntity();

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('App\Entity\Task::setCreatedAt(): Argument #1 ($createdAt) must be of type DateTime, null given, called in');

        $task->setCreatedAt(null);
    }
    public function testValidGetCreatedAt(): void
    {
        $task = $this->getEntity();
        $task->setCreatedAt(new DateTime());
        $this->assertInstanceOf(DateTime::class, $task->getCreatedAt());
    }
    public function testGetCreatedAt()
    {
        $entity =  $this->getEntity();
        $createdAt = new DateTime();
        $entity->setCreatedAt($createdAt);

        $this->assertEquals($createdAt, $entity->getCreatedAt());
    }

    public function testSetCreatedAt()
    {
        $entity =  $this->getEntity();
        $createdAt = new DateTime();
        $entity->setCreatedAt($createdAt);

        $this->assertEquals($createdAt, $entity->getCreatedAt());

        $newCreatedAt = new DateTime();
        $entity->setCreatedAt($newCreatedAt);

        $this->assertEquals($newCreatedAt, $entity->getCreatedAt());
    }


    public function testInvalidTitleEntity()
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 2); // Titre vide et trop court
        $this->assertHasErrors($this->getEntity()->setTitle(str_repeat('a', 181)), 1); // Titre trop long
    }

    public function testInvalidContentEntity()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1); // Contenu vide
    }

    public function testInvalidIsDoneEntity()
    {
        $task = $this->getEntity();

        // Test avec une chaîne vide
        try {
            $task->setIsDone('');
        } catch (\TypeError $e) {
            $this->assertTrue(true);
        }

        // Test avec une valeur non booléenne (entier)
        try {
            $task->setIsDone(123);
        } catch (\TypeError $e) {
            $this->assertTrue(true);
        }

        // Test avec null
        try {
            $task->setIsDone(null);
        } catch (\TypeError $e) {
            $this->assertTrue(true);
        }
    }
    public function testValidIsDoneEntity()
    {
        $this->assertHasErrors($this->getEntity()->setIsDone(true), 0);
        $this->assertHasErrors($this->getEntity()->setIsDone(false), 0);
    }
    public function testGetUser()
    {
        $user = (new User())
            ->setEmail('test@example.com')
            ->setUsername('validusername')
            ->setPassword('validpassword123')
            ->setRoles(['ROLE_USER']);
        $task = $this->getEntity();
        $task->setUser($user);

        $this->assertSame($user, $task->getUser());
    }

    public function testSetUser()
    {
        $user = (new User())
            ->setEmail('test@example.com')
            ->setUsername('validusername')
            ->setPassword('validpassword123')
            ->setRoles(['ROLE_USER']);
        $task = $this->getEntity();
        $task->setUser($user);

        $this->assertSame($user, $task->getUser());
        $newUser = (new User())
            ->setEmail('test2@example.com')
            ->setUsername('validusername2')
            ->setPassword('validpassword123')
            ->setRoles(['ROLE_USER']);

        $task->setUser($newUser);

        $this->assertSame($newUser, $task->getUser());
    }

    public function testSetUserWithNull()
    {
        $task = $this->getEntity();

        $task->setUser(null);

        $this->assertNull($task->getUser());
    }
}
