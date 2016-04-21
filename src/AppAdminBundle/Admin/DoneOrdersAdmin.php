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
 * Class DoneOrdersAdmin
 * @package AppAdminBundle\Admin
 */
class DoneOrdersAdmin extends OrdersAdmin
{
    protected $baseRouteName = 'done_orders';

    protected $baseRoutePattern = '/app/done-orders';

    protected $statusName = 'done';

}
