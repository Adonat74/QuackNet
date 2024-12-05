<?php

namespace App\Repository;

use App\Entity\Quack;
use App\Model\SearchData;
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

    public function findBySearch(SearchData $searchData): array
    {
        $quacks = $this->createQueryBuilder('q')
            ->join('q.duck', 'd')
            ->addSelect('d');


        if (!empty($searchData->query)) {
            $quacks = $quacks
                ->where('q.content LIKE :query')
                ->orWhere('d.duckname LIKE :query')
                ->setParameter('query', '%' . $searchData->query . '%');
        }

        $quacks = $quacks
            ->getQuery()
            ->getResult();

        return $quacks;
    }
}
