<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Orders;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class CanceledOrdersAdmin
 * @package AppAdminBundle\Admin
 */
class CanceledOrdersAdmin extends OrdersAdmin
{
    protected $baseRouteName = 'canceled_orders';

    protected $baseRoutePattern = '/app/canceled-orders';

    protected $statusName = 'canceled';

}
