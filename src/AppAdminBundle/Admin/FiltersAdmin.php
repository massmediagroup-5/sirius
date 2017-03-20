<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class FiltersAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
//            ->add('id')
            ->add('name', null, array('label' => 'Название фильтра'))
            ->add('createTime', 'doctrine_orm_datetime_not_strict', array('label' => 'Время создания'))
            ->add('updateTime', 'doctrine_orm_datetime_not_strict', array('label' => 'Время последнего обновления'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->add('id')
            ->add('name', null, array('label' => 'Название фильтра'))
            ->add('createTime', null, array('label' => 'Время создания'))
            ->add('updateTime', null, array('label' => 'Время последнего обновления'))
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
            ->add('name', null, array('label' => 'Название фильтра'))
            ->end()
            ->with('Значения характеристик',
                array(
                    'class'       => 'col-md-12',
                    'collapsed' => true
                ))
                ->add('characteristics', 'entity',
                    array(
                        'class'    => 'AppBundle:Characteristics' ,
                        'label' => 'Характеристики',
                        'expanded' => true,
                        'multiple' => true,
                        'by_reference' => false
                    )
                )
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
//            ->add('id')
            ->add('name', null, array('label' => 'Название фильтра'))
            ->add('createTime', null, array('label' => 'Время создания'))
            ->add('updateTime', null, array('label' => 'Время последнего обновления'))
        ;
    }
}
