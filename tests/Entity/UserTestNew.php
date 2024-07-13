<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTestNew extends KernelTestCase
{
    private $userRepository;
    private $container;

    public function setUp(): void
    {
        self::bootKernel();
        $this->container = static::getContainer();

        // Mocking the UserRepository
        $this->userRepository = $this->createMock(UserRepository::class);

        // Replace the service in the container with the mock
        $this->container->set(UserRepository::class, $this->userRepository);
    }


    public function testReposWithoutSameUser(): void
    {

        $user = (new User())
            ->setEmail('test@example.com')
            ->setUsername('validusername')
            ->setPassword('validpassword123')
            ->setRoles(['ROLE_USER']);

        $this->userRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('test@example.com')
            ->willReturn(null);

        // Retrieve the service that uses the mocked repository
        $retrievedUser = $this->userRepository->findOneByEmail($user->getEmail());
        $this->assertNull($retrievedUser);
    }

    public function testReposWithSameUser(): void
    {

        $user = (new User())
            ->setEmail('test@example.com')
            ->setUsername('validusername')
            ->setPassword('validpassword123')
            ->setRoles(['ROLE_USER']);

        $this->userRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('test@example.com')
            ->will($this->throwException(new \RuntimeException('UniqueConstraintViolationException')));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('UniqueConstraintViolationException');

        // Retrieve the service that uses the mocked repository
        $retrievedUser = $this->userRepository->findOneByEmail($user->getEmail());
        dd($retrievedUser);
        $this->assertNotNull($retrievedUser);
    }

    public function getEntity(): User
    {
        return (new User())
            ->setEmail('test@example.com')
            ->setUsername('validusername')
            ->setPassword('validpassword123')
            ->setRoles(['ROLE_USER']);
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        $errors = $this->container->get('validator')->validate($user);
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

    public function testInvalidEmailEntity()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1); // Email vide
        $this->assertHasErrors($this->getEntity()->setEmail('invalid-email'), 1); // Email invalide
        $this->assertHasErrors($this->getEntity()->setEmail(str_repeat('a', 65) . '@example.com'), 1); // Email trop long
    }

    public function testInvalidUsernameEntity()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(str_repeat('a', 65)), 1); // Username trop long
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1); // Username vide
        // $this->assertHasErrors($this->getEntity()->setUsername('<script>"Protection"</script>'), 1); // Username avec caractères spéciaux
    }

    public function testInvalidPasswordEntity()
    {
        $this->assertHasErrors($this->getEntity()->setPassword(''), 2); // Mot de passe vide et court
        $this->assertHasErrors($this->getEntity()->setPassword('short'), 1); // Mot de passe trop court
        $this->assertHasErrors($this->getEntity()->setPassword(str_repeat('a', 181)), 1); // Mot de passe trop long
    }

    public function testInvalidRolesEntity()
    {
        $this->assertHasErrors($this->getEntity()->setRoles([]), 0); // Pas d'erreur attendu car ROLE_USER est ajouté par défaut
    }
}
