<?php

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\Orders;
use AppBundle\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    /**
     * @param $user
     * @return array
     */
    public function bonusesInProcess($user)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('sclr_0', 'bonusesInProcess');

        return $this->_em->createNativeQuery('SELECT 
                SUM(ROUND(((SELECT 
                                SUM(sizes.discounted_total_price * sizes.quantity)
                            FROM
                                order_product_size sizes
                            WHERE
                                sizes.order_id = orders.id) - orders.individual_discount + orders.additional_solar - orders.bonuses) / 100)) sclr_0
            FROM orders WHERE orders.users_id = ? AND orders.bonuses_enrolled = 0;', $rsm)
            ->setParameter(1, $user->getId())
            ->getSingleScalarResult();
    }

    /**
     * @param Users $user
     * @param Orders $order
     * @return array
     */
    public function otherOrdersSum(Users $user, Orders $order)
    {

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('sclr_0', 'ordersPrice');

        return $this->_em->createNativeQuery('SELECT 
                SUM(((SELECT 
                                SUM(sizes.discounted_total_price * sizes.quantity)
                            FROM
                                order_product_size sizes
                            WHERE
                                sizes.order_id = orders.id) - orders.individual_discount + orders.additional_solar - orders.bonuses)) sclr_0
            FROM orders WHERE orders.users_id = :user_id AND orders.id <> :id;', $rsm)
            ->setParameter('user_id', $user->getId())
            ->setParameter('id', $order->getId())
            ->getSingleScalarResult();
    }

    /**
     * @param $user
     * @param $order
     * @return \Doctrine\ORM\Query
     */
    public function otherOrdersByUser($user, $order)
    {
        return $this->createQueryBuilder('orders')
            ->where('orders.users = :user_id AND orders.id <> :id')
            ->setParameter('user_id', $user->getId())
            ->setParameter('id', $order->getId())
            ->getQuery();
    }

}
