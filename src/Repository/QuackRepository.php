<?php

namespace App\Repository;

use App\Entity\Quack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quack>
 */
class QuackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quack::class);
    }

    public function findAllQuackByDuckname(string $duckname): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT * FROM quack q
            WHERE q.duckname
            LIKE '%keyword%'
            ";

        $resultSet = $conn->executeQuery($sql, ['duckname' => $duckname]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }
}
