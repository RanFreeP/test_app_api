<?php

namespace App\Repository;

use App\Entity\Models;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Models>
 */
class ModelsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Models::class);
    }

    public function findOneByIdBrand($brand_id)
    {
        if($brand_id === 0 || $brand_id === '0') {
            return null;
        }

        $query = $this->getEntityManager()
            ->createQuery('
            SELECT m, b FROM App\Entity\Models m
            LEFT JOIN m.brand b
            where b.id = :id
            ')
            ->setParameter('id', $brand_id);

        try {
            return $query;
        }catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    //    /**
    //     * @return Models[] Returns an array of Models objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Models
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
