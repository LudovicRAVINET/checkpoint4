<?php

namespace App\Repository;

use App\Entity\UserLicense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserLicense|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLicense|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLicense[]    findAll()
 * @method UserLicense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLicenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLicense::class);
    }

    // /**
    //  * @return UserLicense[] Returns an array of UserLicense objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserLicense
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
