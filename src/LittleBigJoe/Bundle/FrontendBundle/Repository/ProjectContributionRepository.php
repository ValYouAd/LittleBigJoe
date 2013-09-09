<?php

namespace LittleBigJoe\Bundle\FrontendBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProjectContributionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectContributionRepository extends EntityRepository
{
    /**
     * Return the number of projects contributions
     *
     * @param boolean/null $succeeded :
     *        if set to null, return all projects contributions
     *        if set to integer, return all projects contributions with status id
     * @param boolean/null $completed :
     *        if set to null, return all projects contributions
     *        if set to integer, return all projects contributions with status id
     * @return int nbProjectsContributions
     */
    public function count($succeeded = null, $completed = null)
    {
        $qb = $this->createQueryBuilder('pc')->select('COUNT(pc)');

        if (!empty($succeeded)) {
            $qb = $qb->where('pc.mangopayIsSucceeded = :mangopayIsSucceeded')->setParameter('mangopayIsSucceeded', $succeeded);
        }

        if (!empty($completed)) {
            $qb = $qb->andWhere('pc.mangopayIsCompleted = :mangopayIsCompleted')->setParameter('mangopayIsCompleted', $completed);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Return latest projects contributions
     *
     * @param integer $limit : return the $limit project contributions
     * @return array latestProjectContributions
     */
    public function findLatest($limit = 4)
    {
        return $this->createQueryBuilder('pc')
            ->orderBy('pc.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
