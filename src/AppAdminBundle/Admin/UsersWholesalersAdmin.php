<?php

namespace AppAdminBundle\Admin;

use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Route\RouteCollection;


class UsersWholesalersAdmin extends UsersAdmin
{
    protected $baseRouteName = 'users_wholesalers';

    protected $baseRoutePattern = '/app/users-wholesalers';

    protected $userRole = 'ROLE_WHOLESALER';
}