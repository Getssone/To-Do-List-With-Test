<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setCreatedAt($faker->dateTimeBetween('-30 days', 'now'));
            $task->setTitle($faker->title());
            $task->setContent($faker->text());
            $task->setIsDone($faker->boolean);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
