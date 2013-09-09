<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     * Return the number of users
     *
     * @param boolean/null $visible :
     *        if set to null, return all users
     * @return int nbUsers
     */
    public function count()
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return users for specific keyword (used for search)
     *
     * @param string $search : search keyword
     * @return array users
     */
    public function findBySearch($search)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.firstname LIKE :search')
            ->orWhere('u.lastname LIKE :search')
            ->setParameter('search', '%' . $search . '%');

        return $qb->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
