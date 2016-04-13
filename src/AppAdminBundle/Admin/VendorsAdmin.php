<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class VendorsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Название'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего обновления'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, array('label' => 'Название'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего обновления'))
            ->add('_action', 'actions', array(
                'actions' => array(
//                    'show' => array(),
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
            ->add('name', null, array('label' => 'Название'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('description', null, array('label' => 'Описание'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, array('label' => 'Название'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('description', null, array('label' => 'Описание'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего обновления'))
        ;
    }
}
