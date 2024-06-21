<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $someService = $this->container->get(SomeService::class);
        // $someService->doSomething();
        // Votre logique de fixtures ici

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
