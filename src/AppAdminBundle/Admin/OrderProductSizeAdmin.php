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
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('createTime',
                'doctrine_orm_datetime_range',
                array('label'=>'Время заказа','field_type' => 'sonata_type_datetime_range_picker'),
                null,
                array(
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy H:m',
                    'required' => false,
                    'attr' => ['class' => 'date_time_selector']
                )
            )
            ->add('order', null,
                ['label'=>'Заказ'],
                null,
                [
                    'property'=>'StringForFilter',
                    'multiple' => true
                ]
            )
//            ->add('order.type', 'doctrine_orm_choice', array('label' => 'Тип заказа'),
//                'choice',
//                [
//                    'choices' => [
//                        '' => 'Не указанно',
//                        (string)Orders::TYPE_NORMAL => 'Обычный',
//                        (string)Orders::TYPE_QUICK => 'Быстрый',
//                    ]
//                ]
//            )
            ->add('order.status', null, [
                'label' => 'Статус заказа'
            ])
            ->add('order.users', null, [
                'label' => 'Пользователи'
            ])
        ;
    }


    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('order.identifier', null,['label'=>'Заказ №'])
            ->add('order.type', 'choice', [
                'label' => 'Тип заказа',
                'choices' => [
                    '' => 'Не указанно',
                    (string)Orders::TYPE_NORMAL => 'Обычный',
                    (string)Orders::TYPE_QUICK => 'Быстрый',
                ]
            ])
            ->add('order.status.name', null, [
                'label' => 'Статус заказа'
            ])
            ->add('size.model.products.article', null, ['label' => 'Артикул'])
            ->add('size.model.products.name', null, ['label' => 'Название'])
            ->add('size.model.productColors.name', null, ['label' => 'Цвет'])
            ->add('size.size', null, ['label' => 'Размер'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('totalPrice', 'text', ['label' => 'Цена'])
            ->add('discountedTotalPrice', 'text', ['label' => 'Цена со скидкой'])
            ->add('createTime', null, ['label' => 'Дата создания(заказа)'])
            ->add('updateTime', null, ['label' => 'Дата последнего редактирования(заказа)'])
            ->add('_action', 'actions', [
                'actions' => [
//                    'show' => [],
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
            ->add('size.model.productColors.name', null, ['label' => 'Цвет', 'disabled' => true])
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

    public function getExportFields()
    {
        $exportFields["Заказ №"] = 'order.identifier';
        $exportFields["Статус заказа"] = 'order.status.name';
        $exportFields["Название товара"] = 'size.model.products.name';
        $exportFields["Цвет"] = 'size.model.productColors.name';
        $exportFields["Артикул"] = 'size.model.products.article';
        $exportFields["Размер"] = 'size.size';
        $exportFields["Количество"] = 'quantity';
        $exportFields["Цена"] = 'totalPrice';
        $exportFields["Цена со скидкой"] = 'discountedTotalPrice';
        return $exportFields;
    }

}
