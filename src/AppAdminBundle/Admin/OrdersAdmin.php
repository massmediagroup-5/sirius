<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Orders;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class OrdersAdmin
 * @package AppAdminBundle\Admin
 */
class OrdersAdmin extends Admin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'createTime'  // name of the ordered field

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    ];

    /**
     * @var string
     */
    protected $statusName = 'new';

    /**
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAliases()[0];
        $query->innerJoin("$alias.status", 'status');
        $query->andWhere('status.code = :status')->setParameter('status', $this->statusName);
        return $query;
    }

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
            ->add('type', null, ['label' => 'Тип заказа'])
            ->add('fio', null, ['label' => 'Ф.И.О.'])
            ->add('phone', null, ['label' => 'Телефон'])
            ->add('pay', null, ['label' => 'Способ оплаты'])
            ->add('totalPrice', null, ['label' => 'Способ оплаты'])
            ->add('createTime', null, ['label' => 'Время оформления'])
            ->add('updateTime', null, ['label' => 'Время последнего редактирования заказа']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->addIdentifier('type', 'choice', [
                'label' => 'Тип заказа',
                'route' => ['name' => 'edit'],
                'choices' => [
                    (string)Orders::TYPE_NORMAL => 'Обычный',
                    (string)Orders::TYPE_QUICK => 'Быстрый',
                ]
            ])
            ->addIdentifier('fio', null, ['label' => 'Ф.И.О.', 'route' => ['name' => 'edit']])
            ->addIdentifier('phone', null, ['label' => 'Телефон', 'route' => ['name' => 'edit']])
            ->add('pay', 'choice', [
                'label' => 'Способ оплаты',
                'choices' => [
                    (string)Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                    (string)Orders::PAY_TYPE_COD => 'Наложеным платежом',
                ]
            ])
            ->add('carriers.name', null, ['label' => 'Способ доставки'])
            ->add('cities.name', null, ['label' => 'Город'])
            ->add('stores.name', null, ['label' => 'Адрес склада'])
            ->add('totalPrice', null, ['label' => 'Сумма заказа'])
            ->add('createTime', null, ['label' => 'Время оформления'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Заказ')
            ->with('Orders',
                [
                    'class' => 'col-md-12',
                ])
            ->add('status', 'entity',
                [
                    'class' => 'AppBundle:OrderStatus',
                    'property' => 'name',
                    'label' => 'Сатус заказа',
                    'empty_value' => 'Выберите статус заказа',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->orderBy('s.priority', 'ASC')
                            ->where('s.priority >= :priority')
                            ->setParameter('priority',
                                $this->getSubject()->getStatus()->getPriority())->setMaxResults(2);
                    }
                ]
            )
            ->add('type', 'choice', [
                'label' => 'Тип заказа',
                'read_only' => true,
                'disabled' => true,
                'choices' => [
                    (string)Orders::TYPE_NORMAL => 'Обычный',
                    (string)Orders::TYPE_QUICK => 'Быстрый',
                ]
            ])
            ->add('fio', null, [
                'label' => 'Ф.И.О.',
            ])
            ->add('phone', null, [
                'label' => 'Телефон',
            ])
            ->add('pay', 'choice', [
                'label' => 'Способ оплаты',
                'choices' => [
                    (string)Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                    (string)Orders::PAY_TYPE_COD => 'Наложеным платежом',
                ]
            ])
            ->add('totalPrice', null, [
                'label' => 'Сумма заказа',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('clientSmsId', null, [
                'label' => 'Идентификатор смс клиента',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('clientSmsStatus', null, [
                'label' => 'Статус смс клиента',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('managerSmsId', null, [
                'label' => 'Идентификатор смс менеджера',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('managerSmsStatus', null, [
                'label' => 'Статус смс менеджера',
                'read_only' => true,
                'disabled' => true,
            ])
            ->add('comment', null, [
                'label' => 'Коментарий к заказу',
            ])
            ->add('comment_admin', null, [
                'label' => 'Коментарий к заказу для администратора',
            ])
            ->end()
            ->end()
            ->tab('Список заказанных товаров', [
                'tab_template' => 'AppAdminBundle:admin:order_sizes.html.twig'
            ])
            ->add('individualDiscount', null, [
                'label' => 'Индивидуальная скидка',
            ])
            ->end();
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'show':
                return 'AppAdminBundle:admin:order_show.html.twig';
                break;

            case 'edit':
                return 'AppAdminBundle:admin:order_edit.html.twig';
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
                [
                    'class' => 'col-md-12',
                ])
            ->add('id')
            ->add('status', 'entity',
                [
                    'class' => 'AppBundle:OrderStatus',
                    'associated_property' => 'name',
                    'label' => 'Сатус заказа',
                    'empty_value' => 'Выберите статус заказа'
                ]
            )
            ->add('type', null, ['label' => 'Тип заказа'])
            ->add('customDelivery', null, ['label' => '"Своя доставка"'])
            ->add('comment', null, ['label' => 'Коментарий к заказу'])
            ->add('comment_admin', null, ['label' => 'Коментарий к заказу для администратора'])
            ->add('fio', null, ['label' => 'Ф.И.О.'])
            ->add('phone', null, ['label' => 'Телефон'])
            ->add('pay', null, ['label' => 'Способ оплаты'])
            ->add('totalPrice', null, ['label' => 'Сумма заказа'])
            ->add('clientSmsId', null, ['label' => 'Идентификатор смс клиента'])
            ->add('clientSmsStatus', null, ['label' => 'Статус смс клиента'])
            ->add('managerSmsId', null, ['label' => 'Идентификатор смс менеджера'])
            ->add('managerSmsStatus', null, ['label' => 'Статус смс менеджера'])
            ->add('createTime', null, ['label' => 'Время оформления'])
            ->add('updateTime', null, ['label' => 'Время последнего редактирования заказа'])
            ->end()
            ->end()
            ->tab('Список заказанных товаров')
            ->with('Cart',
                [
                    'class' => 'col-md-12',
                ])->add('cart', 'sonata_type_collection', [
                'label' => 'Модель',
                'required' => false,
                'cascade_validation' => true,
                'associated_property' => 'skuProducts.name',
                'by_reference' => false
            ], ['edit' => 'inline', 'inline' => 'table'])
            ->end()
            ->end();
    }
}
