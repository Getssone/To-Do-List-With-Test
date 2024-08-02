<?php

namespace App\Repository;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepositoryTest extends KernelTestCase
{

    public function testCount()
    {
        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $tasksCount = $entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(Task::class, 'u')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(10, $tasksCount);
    }
}
