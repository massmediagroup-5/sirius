<?php

namespace AppBundle\Entity\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ShareRepository
 * @package AppBundle\Entity\Repository
 */
class OrdersRepository extends EntityRepository
{

    /**
     * @param $interval
     * @return array
     */
    public function findToAppendBonuses($interval)
    {
        return $this->createQueryBuilder('orders')
            ->where('orders.doneTime <= :lastDate AND orders.bonusesEnrolled = :bonusesEnrolled AND ' .
                'orders.users IS NOT NULL')
            ->setParameters([
                'lastDate' => (new \DateTime())->modify("-$interval days"),
                'bonusesEnrolled' => false,
            ])
            ->getQuery()
            ->getResult();
    }

}
