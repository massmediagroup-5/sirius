<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class CartAdmin extends Admin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
        ;

    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('orders.fio', null, array('label' => 'Ф.И.О.'))
            ->add('orders.phone', null, array('label' => 'Телефон'))
            ->add('orders.type', null, array('label' => 'Тип заказа'))
            ->add('status', null, array('label' => 'Статус заказа'))
            ->add('quantity', null, array('label' => 'Количество'))
            ->add('createTime', null, array('label' => 'Дата создания(заказа)'))
            ->add('updateTime', null, array('label' => 'Дата последнего редактирования(заказа)'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('orders.fio', null, array('label' => 'Ф.И.О.'))
            ->addIdentifier('orders.phone', null, array('label' => 'Телефон'))
            ->add('orders.type', null, array('label' => 'Тип заказа'))
            ->add('skuProducts.productModels.name', null, array('label' => 'Имя модели'))
            ->add('status', 'choice',  array(
                'label' => 'Статус заказа',
                'choices' => array(
                    '0' => 'Ожидает обработки',
                    '1' => 'Принят',
                    '2' => 'Отклонен'
                )
            ))
            ->add('quantity', null, array('label' => 'Количество'))
            ->add('createTime', null, array('label' => 'Дата создания(заказа)'))
            ->add('updateTime', null, array('label' => 'Дата последнего редактирования(заказа)'))
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
            ->add('orders.fio','text',array(
                'label' => 'Ф.И.О. покупателя',
                'read_only' => true,
                'disabled'  => true,
            ))
            ->add('skuProducts.name', 'text'
                ,array('label' => 'Название модели',
                    'read_only' => true,
                    'disabled'  => true,
                )
            )
            ->add('status', 'choice',  array(
                'label' => 'Статус заказа',
                'choices' => array(
                    '0' => 'Ожидает обработки',
                    '1' => 'Принят',
                    '2' => 'Отклонен'
                )
            ))
            ->add('quantity', null, array(
                    'label' => 'Количество',
                    'read_only' => true,
                    'disabled'  => true,
                )
            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('skuProducts.name', null, array('label' => 'Статус заказа'))
            ->add('status', null, array('label' => 'Название модели'))
            ->add('quantity', null, array('label' => 'Количество'))
            ->add('createTime', null, array('label' => 'Дата создания(заказа)'))
            ->add('updateTime', null, array('label' => 'Дата последнего редактирования(заказа)'))
        ;
    }
}
