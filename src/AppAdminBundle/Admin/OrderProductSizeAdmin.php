<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Orders;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class: OrderProductSizeAdmin
 *
 * @see Admin
 */
class OrderProductSizeAdmin extends Admin
{

    /**
     * @var string
     */
    protected $parentAssociationMapping = 'order';

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('order.fio', null, array('label' => 'Ф.И.О.'))
            ->addIdentifier('order.phone', null, array('label' => 'Телефон'))
            ->add('order.type', 'choice', [
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
     * configureFormFields
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('size.model.products.article', null, ['label' => 'Размер'])
            ->add('size.model.products.name', null, ['label' => 'Размер'])
            ->add('size.model.productColors.name', null, ['label' => 'Размер'])
            ->add('size.size', null, ['label' => 'Размер'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('totalPrice', null, ['label' => 'Цена', 'required' => true])
            ->add('discountedTotalPrice', null, ['label' => 'Цена со скидкой']);
    }

    /**
     * configureShowFields
     *
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('size.size', null, ['label' => 'Размер'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('totalPrice', null, ['label' => 'Цена', 'required' => true])
            ->add('discountedTotalPrice', null, ['label' => 'Цена со скидкой']);
    }
}
