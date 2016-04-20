<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CharacteristicValuesAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Название'))
            ->add('inFilter', null, array('label' => 'В фильтре'))
//            ->add('notParse', null, array('label' => 'Не парсить'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, array('label' => 'Название значения характеристики'))
            ->add('characteristics.name', null, array('label' => 'Название характеристики'))
            ->add('inFilter', null, array('label' => 'В фильтре'))
//            ->add('notParse', null, array('label' => 'Не парсить'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'))
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
            ->add('characteristics', null, array('label' => 'Характеристика'))
            ->add('name', null, array('label' => 'Значение'))
            ->add('inFilter', null, array('label' => 'В фильтре'))
//            ->add('notParse', null, array('label' => 'Не спарсилась'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, array('label' => 'Название'))
            ->add('inFilter', null, array('label' => 'В фильтре'))
//            ->add('notParse', null, array('label' => 'Не парсить'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'))
        ;
    }
}
