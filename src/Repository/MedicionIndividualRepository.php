<?php

namespace App\Repository;

use App\Entity\MedicionIndividual;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MedicionIndividual|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicionIndividual|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicionIndividual[]    findAll()
 * @method MedicionIndividual[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicionIndividualRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicionIndividual::class);
    }

    // /**
    //  * @return MedicionIndividual[] Returns an array of MedicionIndividual objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?MedicionIndividual
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
