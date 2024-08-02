<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findByStatus(bool $isDone): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isDone = :val')
            ->setParameter('val', $isDone)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findAllDESC(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }
    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findOneByEmail($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.email = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneByEmail($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.email = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
