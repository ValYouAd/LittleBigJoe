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
}
