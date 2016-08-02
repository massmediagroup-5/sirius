<?php

namespace AppAdminBundle\Admin;


class UsersWholesalersAdmin extends UsersAdmin
{
    protected $baseRouteName = 'users_wholesalers';

    protected $baseRoutePattern = '/app/users-wholesalers';

    protected $userRole = 'ROLE_WHOLESALER';
}