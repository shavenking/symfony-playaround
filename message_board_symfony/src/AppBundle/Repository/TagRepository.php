<?php

namespace AppBundle\Repository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        // create query
        $query = $qb->select('t')
            ->from('AppBundle:Tag', 't')
            ->getQuery();

        return $query->getResult();
    }
}
