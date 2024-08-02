<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail('user' . $i . '@testy.fr');
            $user->setUsername('user' . $i);
            $user->setPlainPassword('0000');
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
