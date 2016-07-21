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
            ->addIdentifier('order.fio', null, ['label' => 'Ф.И.О.'])
            ->addIdentifier('order.phone', null, ['label' => 'Телефон'])
            ->add('order.type', 'choice', [
                'label' => 'Тип заказа',
                'choices' => [
                    (string)Orders::TYPE_NORMAL => 'Обычный',
                    (string)Orders::TYPE_QUICK => 'Быстрый',
                ]
            ])
            ->add('order.status.name', null, [
                'label' => 'Статус заказа'
            ])
            ->add('createTime', null, ['label' => 'Дата создания(заказа)'])
            ->add('updateTime', null, ['label' => 'Дата последнего редактирования(заказа)'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * configureFormFields
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('size.model.products.article', null, ['label' => 'Размер', 'disabled' => true])
            ->add('size.model.products.name', null, ['label' => 'Размер', 'disabled' => true])
            ->add('size.model.productColors.name', null, ['label' => 'Размер', 'disabled' => true])
            ->add('size.size', null, ['label' => 'Размер', 'disabled' => true])
            ->add('quantity', null, ['label' => 'Количество', 'disabled' => true])
            ->add('totalPrice', 'text', ['label' => 'Цена', 'disabled' => true])
            ->add('discountedTotalPrice', 'text', ['label' => 'Цена со скидкой', 'disabled' => true]);
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
