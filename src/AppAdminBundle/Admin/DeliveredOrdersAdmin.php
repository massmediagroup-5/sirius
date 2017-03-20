<?php

namespace AppAdminBundle\Admin;


use Doctrine\ORM\EntityRepository;

/**
 * Class NewOrdersAdmin
 * @package AppAdminBundle\Admin
 */
class DeliveredOrdersAdmin extends OrdersAdmin
{
    protected $baseRouteName = 'delivered_orders';

    protected $baseRoutePattern = '/app/delivered-orders';

    protected $statusName = 'delivered';

    /**
     * @inheritdoc
     */
    protected function getStatusQuery()
    {
        return function (EntityRepository $er) {
            return $er->createQueryBuilder('s')
                ->where('s.code IN (:codes)')
                ->setParameter('codes', ['done', 'delivered', 'return', 'refused'])
                ->orderBy('s.priority');
        };
    }

}
