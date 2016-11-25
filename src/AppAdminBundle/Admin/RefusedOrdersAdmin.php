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
 * Class RefusedOrdersAdmin
 * @package AppAdminBundle\Admin
 */
class RefusedOrdersAdmin extends OrdersAdmin
{
    protected $baseRouteName = 'refused_orders';

    protected $baseRoutePattern = '/app/refused-orders';

    protected $statusName = 'refused';

    /**
     * @inheritdoc
     */
    protected function getStatusQuery()
    {
        return function (EntityRepository $er) {
            return $er->createQueryBuilder('s')
                ->where('s.code = :code')
                ->setParameter('code', 'refused');
        };
    }
}
