<?php

namespace App\Repository;

use App\Entity\MedicionGenerica;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MedicionGenerica|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicionGenerica|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicionGenerica[]    findAll()
 * @method MedicionGenerica[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicionGenericaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicionGenerica::class);
    }

    // /**
    //  * @return MedicionGenerica[] Returns an array of MedicionGenerica objects
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
    public function findOneBySomeField($value): ?MedicionGenerica
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
