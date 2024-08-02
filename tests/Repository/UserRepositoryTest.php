<?php

namespace App\tests\Repository;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    public function testCount()
    {
        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $usersCount = $entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(User::class, 'u')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(12, $usersCount);
    }
    public function testFindAllDESC()
    {
        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $repository = $entityManager->getRepository(User::class);
        $users = $repository->findAllDESC();
        $this->assertCount(12, $users);
    }
}
