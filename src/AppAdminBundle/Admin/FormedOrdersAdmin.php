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
class FormedOrdersAdmin extends OrdersAdmin
{
    protected $baseRouteName = 'formed_orders';

    protected $baseRoutePattern = '/app/formed-orders';

    protected $statusName = 'formed';

    /**
     * @inheritdoc
     */
    protected function getStatusQuery()
    {
        return function (EntityRepository $er) {
            return $er->createQueryBuilder('s')
                ->where('s.code IN (:codes)')
                ->setParameter('codes', ['waiting_for_departure', 'formed', 'canceled']);
        };
    }
}
