<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Message;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends \Doctrine\ORM\EntityRepository
{
    public function find($entityId)
    {
        $em = $this->getEntityManager();
        $entity = Message::class;

        $qb = $em->createQueryBuilder();

        // create query
        $query = $qb->select('m')
            ->from($entity, 'm')
            ->where($qb->expr()->eq('m.id', ':id'))
            ->setParameter('id', $entityId)
            ->getQuery();

        return $query->getSingleResult();
    }

    public function findAllTopLevel()
    {
        $em     = $this->getEntityManager();
        $entity = Message::class;

        $qb = $em->createQueryBuilder();

        // create query
        $query = $qb->select('m')
            ->from($entity, 'm')
            ->where($qb->expr()->isNull('m.parentId'))
            ->getQuery();

        return $query->getResult();
    }
}
