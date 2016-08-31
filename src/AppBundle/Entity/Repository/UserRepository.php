<?php

namespace AppBundle\Entity\Repository;

/**
 * Class UserRepository
 * @package AppBundle\Entity\Repository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $days
     * @return array
     */
    public function findAllWithLastOrderOlderThan($days)
    {
        return $this->createQueryBuilder('users')
            ->join('users.orders', 'orders')
            ->where('DATEDIFF(NOW(), orders.createTime) > :days AND users.createTime > :days')
            ->setParameter('days', $days)
            ->getQuery()
            ->getResult();
    }

}
