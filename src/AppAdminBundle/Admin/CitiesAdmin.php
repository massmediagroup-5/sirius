<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Class: CitiesAdmin
 *
 * @see Admin
 */
class CitiesAdmin extends Admin
{
    /**
     * configureDatagridFilters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Имя']);
    }
}
