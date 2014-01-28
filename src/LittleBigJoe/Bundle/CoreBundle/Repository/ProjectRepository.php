<?php

namespace LittleBigJoe\Bundle\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends EntityRepository
{
    /**
     * Return the number of projects
     *
     * @param boolean/null $deleted :
     *        if set to null, return all projects
     *        if set to true, return all deleted projects
     *        if set to false, return all not deleted projects
     * @param integer/null $status :
     *        if set to null, return all projects
     *        if set to integer, return all projects with status id
     * @param boolean/null $current :
     *        if set to null, return all projects
     *        if set to true, return all current projects
     *        if set to false, return all ended projects
     * @param integer/null $brandId :
     *        if set to null, return all projects
     *        if set to integer, return all projects with brand id
     * @return int nbProjects
     */
    public function count($deleted = null, $status = null, $current = null, $brandId = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p)');

        if (is_bool($deleted) && $deleted) {
            $qb = $qb->andWhere('p.deletedAt IS NOT NULL');
        } else if (is_bool($deleted) && !$deleted) {
            $qb = $qb->andWhere('p.deletedAt IS NULL');
        }

        if (!empty($status)) {
            $qb = $qb->andWhere('p.status = :status')
                ->setParameter('status', $status);
        }

        if (is_bool($current) && $current) {
            $qb = $qb->andWhere('p.endingAt > :now')
                ->setParameter('now', new \Datetime());
        } else if (is_bool($current) && !$current) {
            $qb = $qb->andWhere('p.endingAt <= :now')
                ->setParameter('now', new \Datetime());
        }

        if (!empty($brandId)) {
            $qb = $qb->andWhere('p.brand = :brand')
                ->setParameter('brand', $brandId);
        }

        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return the number of likes for specific brand
     *
     * @param integer/null $brandId :
     *        if set to null, return all likes
     *        if set to integer, return all likes with brand id
     * @return int totalLikes
     */
    public function countLikes($brandId = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.likesCount)')
            ->where('p.deletedAt IS NULL');

        if (!empty($brandId)) {
            $qb = $qb->andWhere('p.brand = :brand')
                ->setParameter('brand', $brandId);
        }

        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Return the number of amount for specific brand
     *
     * @param integer/null $brandId :
     *        if set to null, return all amount
     *        if set to integer, return all amount with brand id
     * @return int totalAmount
     */
    public function countAmount($brandId = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.amountCount)')
            ->where('p.deletedAt IS NULL');

        if (!empty($brandId)) {
            $qb = $qb->andWhere('p.brand = :brand')
                ->setParameter('brand', $brandId);
        }

        return $qb->getQuery()
            ->getSingleScalarResult();
    }
    
    /**
     * Find by slug, indepently from the language
     *
     * @param string $slug : contains slug
     * @return object entity
     */
    public function findBySlugI18n($slug)
    {
        return $this->getEntityManager()
            ->createQuery('
                        SELECT p
                        FROM LittleBigJoeCoreBundle:Project p
                        WHERE p.deletedAt IS NULL
                        AND p.slug LIKE :slug
                    ')
            ->setParameter('slug', '%' . $slug . '%')
            ->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            )
            ->getSingleResult();
    }
    
    /**
     * Return users followers projects by user
     *
     * @param array $followersIds : users ids followed by current user
     * @param integer/null $limit : return the $limit latest projects
     * @return array followersProjects
     */
    public function findUsersFollowersProjects($followersIds, $limit = 4)
    {
        $qb = $this->createQueryBuilder('p')
                    ->where('p.deletedAt IS NULL')
                    ->andWhere('p.user IN (:followersIds)')
                    ->setParameter('followersIds', $followersIds)
                    ->orderBy('p.id', 'DESC');
            
        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);
    
        return $qb->getQuery()
                ->getResult();
    }
    
    /**
     * Return brands followers projects by user
     *
     * @param array $followersIds : users ids followed by current user
     * @param integer/null $limit : return the $limit latest projects
     * @return array followersProjects
     */
    public function findBrandsFollowersProjects($followersIds, $limit = 4)
    {
        $qb = $this->createQueryBuilder('p')
                    ->where('p.deletedAt IS NULL')
                    ->andWhere('p.brand IN (:followersIds)')
                    ->setParameter('followersIds', $followersIds)
                    ->orderBy('p.id', 'DESC');
    
        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);
    
        return $qb->getQuery()
        ->getResult();
    }
    
    /**
     * Return supported projects by user
     * 
     * @param integer $userId : user id
     * @param integer/null $limit : return the $limit latest projects
     * @return array supportedProjects
     */
    public function findSupported($userId, $limit = 4)
    {
        $qb = $this->createQueryBuilder('p')
                    ->leftJoin('p.likes', 'pl')
                    ->leftJoin('p.contributions', 'pc')
                    ->where('p.deletedAt IS NULL')
                    ->andWhere('pl.user = :user OR pc.user = :user')
                    ->setParameter('user', $userId)
                    ->orderBy('p.id', 'DESC');
    
        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);
    
        return $qb->getQuery()
                ->getResult();
    }
    
    /**
     * Return latest projects
     *
     * @param integer/null $limit : return the $limit latest projects
     * @return array latestProjects
     */
    public function findLatest($limit = 4)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->orderBy('p.id', 'DESC');

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Return popular projects
     *
     * @param integer $limit : return the $limit popular projects
     * @param string/null $period :
     *      if set to null, return all popular projects
     *        if set to string, return all popular projects within a period
     * @return array popularProjects
     */
    public function findPopular($limit = 4, $period = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('COUNT(pl) AS HIDDEN nbLikes, COUNT(pc) AS HIDDEN nbContributions')
            ->leftJoin('p.likes', 'pl')
            ->leftJoin('p.contributions', 'pc')
            ->where('p.deletedAt IS NULL');

        if (!empty($period)) {
            $qb = $qb->andWhere('pl.createdAt > :createdAt')
                ->orWhere('pc.createdAt > :createdAt')
                ->setParameter('createdAt', new \DateTime($period));
        }

        return $qb->groupBy('pl.project')
            ->addGroupBy('pc.project')
            ->orderBy('nbLikes', 'DESC')
            ->addOrderBy('nbContributions', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return projects to fund
     *
     * @param integer $limit : return the $limit projects to fund
     * @return array fundingProjects
     */
    public function findFunding($limit = 5)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.status = :status')
            ->setParameter('status', 2)
            ->orderBy('p.id', 'DESC');

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Return favorite projects
     *
     * @param integer $limit : return the $limit favorite projects
     * @param integer $brandId : brand ud
     * @return array favoriteProjects
     */
    public function findFavorite($limit = 4, $brandId = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.isFavorite = :isFavorite')
            ->setParameter('isFavorite', '1')
            ->orderBy('p.id', 'DESC');

        if (!empty($brandId)) {
            $qb = $qb->andWhere('p.brand = :brand')
                ->setParameter('brand', $brandId);
        }

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Return recently updated projects
     *
     * @param integer $limit : return the $limit recently updated projects
     * @return array recentlyUpdatedProjects
     */
    public function findRecentlyUpdated($limit = 5)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->orderBy('p.updatedAt', 'DESC');

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Return top funded projects
     *
     * @param integer $limit : return the $limit top funded projects
     * @return array topFundedProjects
     */
    public function findTopFunded($limit = 4)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.status = :status')
            ->setParameter('status', 2)
            ->orderBy('p.amountCount', 'DESC');

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Return almost ending projects
     *
     * @param integer $limit : return the $limit almost ending projects
     * @return array almostEndingProjects
     */
    public function findAlmostEnding($limit = 4, $period = '+10 days')
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->andwhere('p.status = :status')
            ->setParameter('status', 2)
            ->andWhere('p.endingAt BETWEEN :now AND :future')
            ->setParameter('now', new \DateTime())
            ->setParameter('future', new \DateTime($period))
            ->orderBy('p.id', 'DESC');

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Return current projects for specific brand
     *
     * @param integer $limit : return the $limit current projects for specific brand
     * @param integer $brandId : brand id
     * @return array currentProjects
     */
    public function findCurrent($limit = 4, $brandId = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.endingAt > :now')
            ->setParameter('now', new \Datetime());

        if (!empty($brandId)) {
            $qb = $qb->andWhere('p.brand = :brand')
                ->setParameter('brand', $brandId);
        }

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Return ended projects for specific brand
     *
     * @param integer $limit : return the $limit ended projects for specific brand
     * @param integer $brandId : brand id
     * @return array currentProjects
     */
    public function findEnded($limit = 4, $brandId = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.endingAt <= :now')
            ->setParameter('now', new \Datetime());

        if (!empty($brandId)) {
            $qb = $qb->andWhere('p.brand = :brand')
                ->setParameter('brand', $brandId);
        }

        if (!empty($limit))
            $qb = $qb->setMaxResults($limit);

        return $qb->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Return projects for specific keyword (used for search)
     *
     * @param string $search : search keyword
     * @return array projects
     */
    public function findBySearch($search)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.deletedAt IS NULL')
            ->andWhere('p.name LIKE :search')
            ->setParameter('search', '%' . $search . '%');

        return $qb->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
