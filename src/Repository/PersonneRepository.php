<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personne>
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }
    public function findByAgeInterval($ageMin,$ageMax): array
    {
            $qB=$this->createQueryBuilder('p');
            $this->addInterval($qB,$ageMin,$ageMax);
            return $qB->getQuery()->getResult();
    }
    public function statsByAgeInterval($ageMin,$ageMax): array
    {
        $qB=$this->createQueryBuilder('p');
        $qB->select('avg(p.age) as ageMoyen,count(p.id) as nbrPersonne');
        $this->addInterval($qB,$ageMin,$ageMax);
        return $qB->getQuery()->getScalarResult();
    }

    private function addInterval(QueryBuilder $qB,$ageMin,$ageMax)
    {
        $qB->andWhere('p.age >= :ageMin and p.age <= :ageMax')
            ->setParameter('ageMin', $ageMin)
            ->setParameter('ageMax', $ageMax);

    }


    //    /**
    //     * @return Personne[] Returns an array of Personne objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Personne
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
