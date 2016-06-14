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
 * Class DoneOrdersAdmin
 * @package AppAdminBundle\Admin
 */
class DoneOrdersAdmin extends OrdersAdmin
{
    protected $baseRouteName = 'done_orders';

    protected $baseRoutePattern = '/app/done-orders';

    protected $statusName = 'done';

    /**
     * @inheritdoc
     */
    protected function getStatusQuery()
    {
        return function (EntityRepository $er) {
            return $er->createQueryBuilder('s')
                ->where('s.code = :code')
                ->setParameter('code', 'done');
        };
    }
}
