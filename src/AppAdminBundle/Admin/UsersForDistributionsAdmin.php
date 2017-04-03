<?php

namespace AppAdminBundle\Admin;


/**
 * Class UsersForDistributionsAdmin
 *
 * @package AppAdminBundle\Admin
 */
class UsersForDistributionsAdmin extends UsersAdmin
{
    protected $baseRouteName = 'users_for_distributions';

    protected $baseRoutePattern = '/app/users-for-distributions';

    /**
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = $this->getModelManager()->createQuery($this->getClass());

        foreach ($this->extensions as $extension) {
            $extension->configureQuery($this, $query, $context);
        }

        $query->andWhere('o.roles NOT LIKE :role_admin')->setParameter('role_admin', '%ROLE_ADMIN%');

        return $query;
    }
}