<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Orders;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class NewOrdersAdmin
 * @package AppAdminBundle\Admin
 */
class WaitingForDepartureOrdersAdmin extends OrdersAdmin
{
    protected $baseRouteName = 'waiting_for_departure_orders';

    protected $baseRoutePattern = '/app/waiting-for-departure-orders';

    protected $statusName = 'waiting_for_departure';

    /**
     * @inheritdoc
     */
    protected function getStatusQuery()
    {
        return function (EntityRepository $er) {
            return $er->createQueryBuilder('s')
                ->where('s.code IN (:codes)')
                ->setParameter('codes', ['waiting_for_departure', 'formed', 'sent', 'canceled']);
        };
    }
}
