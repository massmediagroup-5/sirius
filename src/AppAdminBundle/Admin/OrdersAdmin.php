<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class OrdersAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'createTime'  // name of the ordered field

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );

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
            ->add('status', null, array('label' => 'Статус заказа'))
            ->add('type', null, array('label' => 'Тип заказа'))
            ->add('fio', null, array('label' => 'Ф.И.О.'))
            ->add('phone', null, array('label' => 'Телефон'))
            ->add('pay', null, array('label' => 'Способ оплаты'))
            ->add('totalPrice', null, array('label' => 'Способ оплаты'))
            ->add('createTime', null, array('label' => 'Время оформления'))
            ->add('updateTime', null, array('label' => 'Время последнего редактирования заказа'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->add('status', 'choice',  array(
                'label' => 'Статус заказа',
                'editable' => true,
                'choices' => array(
                    '0' => 'Ожидает обработки',
                    '1' => 'Принят',
                    '2' => 'Отклонен'
                )
            ))
            ->addIdentifier('type', null, array('label' => 'Тип заказа', 'route'=> array('name'=>'edit')))
            ->addIdentifier('fio', null, array('label' => 'Ф.И.О.', 'route'=> array('name'=>'edit')))
            ->addIdentifier('phone', null, array('label' => 'Телефон', 'route'=> array('name'=>'edit')))
            ->add('pay', null, array('label' => 'Способ оплаты'))

            ->add('carriers.name', null, array('label' => 'Способ доставки'))
            ->add('cities.name', null, array('label' => 'Город'))
            ->add('stores.name', null, array('label' => 'Адрес склада'))
            ->add('totalPrice', null, array('label' => 'Сумма заказа'))

            ->add('createTime', null, array('label' => 'Время оформления'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
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
            ->tab('Заказ')
                ->with('Orders',
                    array(
                        'class'       => 'col-md-12',
                    ))
                    ->add('status', 'choice',  array(
                        'label' => 'Статус заказа',
                        'choices' => array(
                            '0' => 'Ожидает обработки',
                            '1' => 'Принят',
                            '2' => 'Отклонен'
                        )
                    ))
                    ->add('type', null, array('label' => 'Тип заказа',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('customDelivery', null, array('label' => '"Своя доставка"',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('comment', null, array('label' => 'Коментарий к заказу',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('fio', null, array('label' => 'Ф.И.О.',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('phone', null, array('label' => 'Телефон',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('pay', null, array('label' => 'Способ оплаты',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('totalPrice', null, array('label' => 'Сумма заказа',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('clientSmsId', null, array('label' => 'Идентификатор смс клиента',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('clientSmsStatus', null, array('label' => 'Статус смс клиента',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('managerSmsId', null, array('label' => 'Идентификатор смс менеджера',
                        'read_only' => true,
                        'disabled'  => true,))
                    ->add('managerSmsStatus', null, array('label' => 'Статус смс менеджера',
                        'read_only' => true,
                        'disabled'  => true,))
                ->end()
            ->end()
            ->tab('Список заказанных товаров')
                ->with('Cart',
                    array(
                        'class'       => 'col-md-12',
                    ))
                    ->add('cart', 'sonata_type_collection',
                        array(
                            'label' => 'Модель',
                            'type_options' => array('delete' => false)
                        ),
                        array(
                            'edit' => 'inline',
                            'inline' => 'table',
                            'by_reference' => false,
                            'sortable'  => 'id'
                        )
                    )
                ->end()
            ->end()
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'show':
                return 'AppAdminBundle:admin:order_show.html.twig';
                break;

            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->tab('Заказ')
                ->with('Orders',
                    array(
                        'class'       => 'col-md-12',
                    ))
                    ->add('id')
                    ->add('status', 'choice',  array(
                        'label' => 'Статус заказа',
                        'choices' => array(
                            '0' => 'Ожидает обработки',
                            '1' => 'Принят',
                            '2' => 'Отклонен'
                        )
                    ))
                    ->add('type', null, array('label' => 'Тип заказа'))
                    ->add('customDelivery', null, array('label' => '"Своя доставка"'))
                    ->add('comment', null, array('label' => 'Коментарий к заказу'))
                    ->add('fio', null, array('label' => 'Ф.И.О.'))
                    ->add('phone', null, array('label' => 'Телефон'))
                    ->add('pay', null, array('label' => 'Способ оплаты'))
                    ->add('totalPrice', null, array('label' => 'Сумма заказа'))
                    ->add('clientSmsId', null, array('label' => 'Идентификатор смс клиента'))
                    ->add('clientSmsStatus', null, array('label' => 'Статус смс клиента'))
                    ->add('managerSmsId', null, array('label' => 'Идентификатор смс менеджера'))
                    ->add('managerSmsStatus', null, array('label' => 'Статус смс менеджера'))
                    ->add('createTime', null, array('label' => 'Время оформления'))
                    ->add('updateTime', null, array('label' => 'Время последнего редактирования заказа'))
                ->end()
            ->end()
            ->tab('Список заказанных товаров')
                ->with('Cart',
                    array(
                        'class'       => 'col-md-12',
                    ))->add('cart', 'sonata_type_collection', array('label' => 'Модель',
                        'required' => false, 'cascade_validation' => true,'associated_property' => 'skuProducts.name',
                        'by_reference' => false), array('edit' => 'inline', 'inline' => 'table'))
                ->end()

            ->end()
        ;
    }
}
