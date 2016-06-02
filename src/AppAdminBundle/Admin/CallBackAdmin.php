<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CallBackAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('phone', null, array('label' => 'Номер телефона'))
            ->add('status', 'doctrine_orm_choice', array('label' => 'Статус'),
                'choice',
                array(
                    'choices' => array(
                        '0' => 'Ожидает',
                        '1' => 'Принят',
                        '2' => 'Отклонен'
                    )
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('phone', null, array('label' => 'Номер телефона'))
            ->add('status', 'choice', array(
                'label' => 'Статус',
                'choices' => array(
                    '0' => 'Ожидает',
                    '1' => 'Принят',
                    '2' => 'Отклонен'
                )
            ))
            ->add('createTime', null, array('label' => 'Время получения'))
            ->add('updateTime', null, array('label' => 'Время последнего обновления'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
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
            ->add('phone', null, array('label' => 'Номер телефона'))
            ->add('status', null, array('label' => 'Статус'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('phone', null, array('label' => 'Номер телефона'))
            ->add('status', null, array('label' => 'Статус'))
        ;
    }
}
