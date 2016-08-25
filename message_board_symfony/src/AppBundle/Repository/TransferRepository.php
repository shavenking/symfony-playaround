<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Transfer;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * TransferRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransferRepository extends \Doctrine\ORM\EntityRepository
{
    public function getPaginator($username, $page = 1, $limit = 10)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $dql = $qb->select('t')
            ->from('AppBundle:Transfer', 't')
            ->leftJoin('t.user', 'u')
            ->where($qb->expr()->eq('u.username', '?1'))
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
            ->setParameter(1, $username)
            ->getQuery();

        $paginator = new Paginator($dql);

        return $paginator;
    }

    public function getLatestBalance($user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $query = $qb->select('partial t.{id, balance}')
            ->from('AppBundle:Transfer', 't')
            ->leftJoin('t.user', 'u')
            ->where($qb->expr()->eq('u.id', $user->getId()))
            ->orderBy($qb->expr()->desc('t.transferedAt'))
            ->addOrderBy($qb->expr()->desc('t.id'))
            ->setMaxResults(1)
            ->getQuery();

        try {
            $latestTransfer = $query->getSingleResult();
        } catch (NoResultException $e) {
            $latestTransfer = new Transfer;
            $latestTransfer->setBalance(0);
        }

        return $latestTransfer->getBalance();
    }
}