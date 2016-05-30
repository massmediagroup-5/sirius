<?php

namespace AppAdminBundle\Admin;

use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Route\RouteCollection;


class UsersManagersAdmin extends UsersAdmin
{
    protected $baseRouteName = 'users_managers';

    protected $baseRoutePattern = '/app/users-managers';

    protected $userRole = 'ROLE_ADMIN';
}