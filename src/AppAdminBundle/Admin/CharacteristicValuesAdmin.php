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
            ->add('name', 'doctrine_orm_callback',
                [
                    'label'       => 'Название',
                    'show_filter' => true,
                    'callback'    => function ($queryBuilder, $alias, $field, $value) {
                        if ( ! $value['value']) {
                            return false;
                        }
                        $queryBuilder
                            ->andWhere($alias . '.name LIKE :value')
                            ->setParameter('value', '%' . $value['value']->getName() . '%');

                        return true;
                    },
                ],
                'entity',
                [
                    'class'         => 'AppBundle:CharacteristicValues',
                    'property'      => 'name',
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('o');
                            $qb->select('o')->groupBy('o.name');

                            return $qb;
                        }
                ]
            )
            ->add('characteristics', null, array('label' => 'Название характеристики'))
            ->add('inFilter', 'doctrine_orm_choice', ['label' => 'В фильтре'],
                'choice',
                [
                    'choices' => [
                        '1'                           => 'Да',
                        '0'                           => 'Нет',
                    ]
                ]
            )
            ->add('createTime', 'doctrine_orm_datetime_not_strict', array('label' => 'Дата создания'))
            ->add('updateTime', 'doctrine_orm_datetime_not_strict', array('label' => 'Дата последнего изменения'))
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
            ->add('name', null, array('label' => 'Значение'))
            ->add('inFilter', null, array('label' => 'В фильтре'))
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
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'))
        ;
    }
}
