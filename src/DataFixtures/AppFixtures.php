<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setUsername('validusername');
        $user->setPlainPassword('validpassword123');
        $user->setRoles(["ROLE_USER", "ROLE_ADMIN"]);

        $manager->persist($user);
        $userAnonyme = new User();
        $userAnonyme->setEmail('anonyme@example.com');
        $userAnonyme->setUsername('anonyme');
        $userAnonyme->setPlainPassword('validpassword123');
        $userAnonyme->setRoles(["ROLE_USER", "ROLE_ANONYME"]);
        $manager->persist($userAnonyme);

        $manager->flush();
    }
}
