<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Property $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Property $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param PropertySearch $search
     * @return \Doctrine\ORM\Query
     */
    public function findAllVisibleQuerySearch(PropertySearch $search) : \Doctrine\ORM\Query
    {
        $query = $this->findVisibleQuery();

        if($search->getMaxPrice()){
           $query =  $query->andWhere('p.price <= :maxPrice')
                            ->setParameter('maxPrice', $search->getMaxPrice());
        }
        if($search->getMinSurface()){
            $query =  $query->andwhere('p.surface >= :minSurface')
                            ->setParameter('minSurface', $search->getMinSurface());
        }

        if ($search->getOptions()->count() > 0){
            $k = 0;
            foreach ($search->getOptions() as $k => $option){
                $k++;
                $query = $query
                        ->andWhere(":option$k MEMBER OF p.options")
                        ->setParameter("option$k", $option);
            }
        }

        return $query->getQuery();
    }

    public function findLatest():array
    {
        return $this->findVisibleQuery()
                    ->setMaxResults(8)
                    ->getQuery()
                    ->getResult();
    }

    private function findVisibleQuery():QueryBuilder
    {
        return $this->createQueryBuilder('p')
                    ->where('p.sold = false');
    }
    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
