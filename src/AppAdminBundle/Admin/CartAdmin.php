<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Orders;
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
            ->remove('delete');

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
            ->add('createTime', null, array('label' => 'Дата создания(заказа)'))
            ->add('updateTime', null, array('label' => 'Дата последнего редактирования(заказа)'));
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
            ->add('orders.type', 'choice', [
                'label' => 'Тип заказа',
                'choices' => [
                    (string)Orders::TYPE_NORMAL => 'Обычный',
                    (string)Orders::TYPE_QUICK => 'Быстрый',
                ]
            ])
            ->add('status', 'choice', array(
                'label' => 'Статус заказа',
                'choices' => [
                    '0' => 'Ожидает обработки',
                    '1' => 'Принят',
                    '2' => 'Отклонен'
                ]
            ))
            ->add('createTime', null, array('label' => 'Дата создания(заказа)'))
            ->add('updateTime', null, array('label' => 'Дата последнего редактирования(заказа)'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('orders.fio', 'text', array(
                'label' => 'Ф.И.О. покупателя',
                'read_only' => true,
                'disabled' => true,
            ))
            ->add('status', 'choice', array(
                'label' => 'Статус заказа',
                'choices' => array(
                    '0' => 'Ожидает обработки',
                    '1' => 'Принят',
                    '2' => 'Отклонен'
                )
            ))
            ->add('sizes', 'sonata_type_collection', array(
                'label' => 'Размер'
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'by_reference' => false,
                'sortable' => 'id',
            ));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('status', null, array('label' => 'Название модели'))
            ->add('createTime', null, array('label' => 'Дата создания(заказа)'))
            ->add('updateTime', null, array('label' => 'Дата последнего редактирования(заказа)'));
    }
}
