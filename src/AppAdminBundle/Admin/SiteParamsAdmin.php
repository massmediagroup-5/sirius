<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SiteParamsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('paramName', null, array('label' => 'Название параметра'))
            ->add('paramValue', null, array('label' => 'Значение параметра'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->add('id')
            ->add('paramName', null, array('label' => 'Название параметра'))
            ->add('paramValue', null, array('label' => 'Значение параметра'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('paramName', null, array('label' => 'Название параметра'))
            ->add('paramValue', null, array('label' => 'Значение параметра'))
        ;
    }

//    /**
//     * @param ShowMapper $showMapper
//     */
//    protected function configureShowFields(ShowMapper $showMapper)
//    {
//        $showMapper
//            ->add('paramName', null, array('label' => 'Название параметра'))
//            ->add('paramValue', null, array('label' => 'Значение параметра'))
//        ;
//    }
}
