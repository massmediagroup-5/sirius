<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Validator\OrderStatusConstraint;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Arr;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Validator\ErrorElement;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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

    protected $formOptions = array(
        'validation_groups' => array()
    );

    protected $sizesPerPage = 15;

    /**
     * @var string
     */
    protected $statusName = 'new';

    /**
     * @var bool
     */
    protected $disableEdit = true;

    /**
     * @param string $context
     *
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
        parent::configureRoutes($collection);
        $collection
            ->remove('create')
            ->remove('delete')
            ->add('move_size', $this->getRouterIdParameter() . '/move_sizes', [], [], [
                'expose' => true
            ])
            ->add('change_size_quantity', $this->getRouterIdParameter() . '/change_size_quantity', [], [], [
                'expose' => true
            ])
            ->add('ajax_update', $this->getRouterIdParameter() . '/ajax_update', [], [], [
                'expose' => true
            ])
            ->add('delivery_data', $this->getRouterIdParameter() . '/delivery_data', [], [], [
                'expose' => true
            ])
            ->add('get_sizes', $this->getRouterIdParameter() . '/get_sizes', [], [], [
                'expose' => true
            ])
            ->add('get_other_orders', $this->getRouterIdParameter() . '/get_other_orders', [], [], [
                'expose' => true
            ])
            ->add('add_sizes', $this->getRouterIdParameter() . '/add_sizes', [], [], [
                'expose' => true
            ])
            ->add('remove_size', $this->getRouterIdParameter() . '/remove_size', [], [], [
                'expose' => true
            ])
            ->add('change_pre_order_flag', $this->getRouterIdParameter() . '/change_pre_order_flag', [], [], [
                'expose' => true
            ])
            ->add('cancel_order', $this->getRouterIdParameter() . '/cancel_order')
            ->add('get_invoice', $this->getRouterIdParameter() . '/get_invoice')
            ->add('cancel_order_change', $this->getRouterIdParameter() . '/cancel_order_change/{history_id}')
            ->add('create_from_callback', 'create_from_callback/{id}', [], []);
        if ($this->statusName == 'waiting_for_departure') {
            $collection->add('ajax_create_waybill', $this->getRouterIdParameter() . '/ajax_create_waybill', [], [], [
                'expose' => true
            ])
                ->add('ajax_update_waybill', $this->getRouterIdParameter() . '/ajax_update_waybill', [], [], [
                    'expose' => true
                ])
                ->add('ajax_print_waybill', $this->getRouterIdParameter() . '/ajax_print_waybill', [], [], [
                    'expose' => true
                ])
                ->add('ajax_delete_waybill', $this->getRouterIdParameter() . '/ajax_delete_waybill', [], [], [
                    'expose' => true
                ])
                ->add('ajax_unbind_waybill', $this->getRouterIdParameter() . '/ajax_unbind_waybill', [], [], [
                    'expose' => true
                ]);
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('type', 'doctrine_orm_choice', array('label' => 'Тип заказа'),
                'choice',
                array(
                    'choices' => array(
                        '' => 'Не указанно',
                        (string)Orders::TYPE_NORMAL => 'Обычный',
                        (string)Orders::TYPE_QUICK => 'Быстрый',
                    )
                )
            )
            ->add('fio', 'doctrine_orm_callback',
                [
                    'label' => 'Ф.И.О.',
                    'show_filter' => true,
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return false;
                        }
                        $queryBuilder
                            ->andWhere($alias . '.fio LIKE :value')
                            ->setParameter('value', '%' . $value['value']->getFio() . '%');

                        return true;
                    },
                ],
                'entity',
                [
                    'class' => 'AppBundle:Orders',
                    'property' => 'fio',
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('o');
                            $qb->select('o')->groupBy('o.fio');

                            return $qb;
                        }
                ]
            )
            ->add('phone', 'doctrine_orm_callback',
                [
                    'label' => 'Телефон',
                    'show_filter' => true,
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!$value['value']) {
                            return false;
                        }
                        $queryBuilder
                            ->andWhere($alias . '.phone LIKE :value')
                            ->setParameter('value', '%' . $value['value']->getPhone() . '%');

                        return true;
                    },
                ],
                'entity',
                [
                    'class' => 'AppBundle:Orders',
                    'property' => 'phone',
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('o');
                            $qb->select('o')->groupBy('o.phone');

                            return $qb;
                        }
                ]
            )
            ->add('pay', 'doctrine_orm_choice', array('label' => 'Способ оплаты'),
                'choice',
                array(
                    'choices' => array(
                        (string)Orders::PAY_TYPE_EMPTY => 'Не выбрано',
                        (string)Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                        (string)Orders::PAY_TYPE_COD => 'Наложеным платежом',
                    )
                )
            )
            ->add('createTime', null, ['label' => 'Время оформления'])
            ->add('updateTime', null, ['label' => 'Время последнего редактирования заказа']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('identifier', null, ['label' => 'ID'])
            ->addIdentifier('type', 'choice', [
                'label' => 'Тип заказа',
                'route' => ['name' => 'edit'],
                'choices' => [
                    '' => 'Не указанно',
                    (string)Orders::TYPE_NORMAL => 'Обычный',
                    (string)Orders::TYPE_QUICK => 'Быстрый',
                ]
            ])
            ->addIdentifier('fio', null, ['label' => 'Ф.И.О.', 'route' => ['name' => 'edit']])
            ->addIdentifier('phone', null, ['label' => 'Телефон', 'route' => ['name' => 'edit']])
            ->add('pay', 'choice', [
                'label' => 'Способ оплаты',
                'choices' => [
                    '' => 'Не выбрано',
                    (string)Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                    (string)Orders::PAY_TYPE_COD => 'Наложеным платежом',
                    (string)Orders::PAY_TYPE_EMPTY => 'Не оплачено',
                ]
            ])
//            ->add('users.roles', null, ['label' => 'Тип пользователя'])
            ->add('users.roles', null,
                [
                    'label' => 'Тип пользователя',
                    'template' => 'AppAdminBundle:list:list.template.roles.html.twig'
                ]
            )
            ->add('cities.name', null, ['label' => 'Город'])
            ->add('stores.name', null, ['label' => 'Адрес склада'])
            ->add('createTime', null, ['label' => 'Время оформления'])
            ->add('_action', 'actions', [
                'actions' => [
//                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $otherOrders = $this->paginateOtherOrders();

        $formMapper
            ->tab('Заказ')
            ->with('Заказ',
                [
                    'class' => 'col-md-12',
                ])
            ->add('status', 'entity',
                [
                    'class' => 'AppBundle:OrderStatus',
                    'property' => 'name',
                    'label' => 'Статус заказа',
                    'empty_value' => 'Выберите статус заказа',
                    'query_builder' => $this->getStatusQuery(),
                    'constraints' => [new OrderStatusConstraint()]
                ]
            )
            ->add('payStatus', 'entity',
                [
                    'class' => 'AppBundle:OrderStatusPay',
                    'property' => 'name',
                    'label' => 'Сатус оплаты заказа',
                    'empty_value' => 'Выберите статус оплаты'
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
                'read_only' => $this->disableEdit,
                'disabled' => $this->disableEdit,
            ])
            ->add('phone', null, [
                'label' => 'Телефон',
                'read_only' => $this->disableEdit,
                'disabled' => $this->disableEdit,
            ])
            ->add('pay', 'choice', [
                'label' => 'Способ оплаты',
                'read_only' => $this->disableEdit,
                'disabled' => $this->disableEdit,
                'choices' => [
                    (string)Orders::PAY_TYPE_EMPTY => 'Не выбрано',
                    (string)Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                    (string)Orders::PAY_TYPE_COD => 'Наложеным платежом',
                ]
            ])
            ->add('carriers', 'entity', [
                'class' => 'AppBundle:Carriers',
                'label' => 'Служба доставки',
                'read_only' => $this->disableEdit,
                'disabled' => $this->disableEdit,
                'empty_value' => 'Выберите службу доставки',
                'attr' => [
                    'class' => 'js-custom-carriers'
                ]
            ])
            ->add('customDelivery', null, [
                'label' => 'Своя доставка',
                'read_only' => $this->disableEdit,
                'disabled' => $this->disableEdit,
                'attr' => [
                    'class' => 'js-custom-delivery'
                ]
            ])
//            ->add('clientSmsId', null, [
//                'label'     => 'Идентификатор смс клиента',
//                'read_only' => true,
//                'disabled'  => true,
//            ])
//            ->add('clientSmsStatus', null, [
//                'label'     => 'Статус смс клиента',
//                'read_only' => true,
//                'disabled'  => true,
//            ])
//            ->add('smsInfo', 'sonata_type_collection', array(), array(
//                'edit' => 'inline',
//                'inline' => 'table',
//                'sortable'  => 'position'
//            ))
//            ->add('managerSmsId', null, [
//                'label' => 'Идентификатор смс менеджера',
//                'read_only' => true,
//                'disabled' => true,
//            ])
//            ->add('managerSmsStatus', null, [
//                'label' => 'Статус смс менеджера',
//                'read_only' => true,
//                'disabled' => true,
//            ])
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
            ->end();
        if (in_array($this->statusName, ['waiting_for_departure', 'sent', 'done', 'canceled'])) {
            $ttn = $date = '';
            if ($this->isCurrentCarrierNovaPoshta()) {
                if ($this->subject->getTtn()) {
                    $this->get('novaposhta')->initConfig();
                    $ttnDocument = $this->get('novaposhta')->getInternetDocumentByRef($this->subject->getTtn());
                    if ($ttnDocument->data) {
                        $dateDocument = $this->get('novaposhta')->getDocumentDeliveryDateByTtnDocument($ttnDocument);
                        $ttn = $ttnDocument->data[0];
                        $date = count($dateDocument->data) ? $dateDocument->data[0]->DeliveryDate : '';
                    }
                }
            }

            $formMapper
                ->tab('ТТН', [
                    'tab_template' => 'AppAdminBundle:admin:order_np_waybill.html.twig',
                    'object' => $this->getSubject(),
                    'ttn' => $ttn,
                    'date' => $date
                ])
                ->add('customTtn', null, ['label' => 'ТТН'])
                ->end()
                ->end();
        }
        if ($otherOrders) {
            $formMapper->tab('Другие заказы покупателя', [
                'tab_template' => 'AppAdminBundle:admin:order_other_sizes.html.twig',
                'otherOrders' => $otherOrders,
                'totalSum' => $this->getOtherOrdersTotalSum()
            ])
                ->end();
        }

        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    /**
     * @param FormEvent $e
     */
    public function onPreSetData(FormEvent $e)
    {
        $this->addCitiesField($e->getForm(), $e->getData() ? $e->getData()->getCarriers() : null);
        $this->addStoresField($e->getForm(), $e->getData() ? $e->getData()->getCities() : null);
    }

    /**
     * @param FormEvent $e
     */
    public function onPreSubmit(FormEvent $e)
    {
        $this->addCitiesField($e->getForm(), Arr::get($e->getData(), 'carriers'));
        $this->addStoresField($e->getForm(), Arr::get($e->getData(), 'cities'));
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
                    'label' => 'Статус заказа',
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
                ])
            ->add('cart', 'sonata_type_collection', [
                'label' => 'Модель',
                'required' => false,
                'cascade_validation' => true,
                'associated_property' => 'skuProducts.name',
                'by_reference' => false
            ], ['edit' => 'inline', 'inline' => 'table'])
            ->end()
            ->end();
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(parent::getFormTheme(), ['AppAdminBundle:Form:sonata_stores_list_edit.html.twig']);
    }

    /**
     * @return bool
     */
    public function isCurrentCarrierNovaPoshta()
    {
        return ($city = $this->subject->getCities()) && $this->get('novaposhta')->isNovaPoshtaCarrier($city->getCarriers());
    }

    /**
     * @param array $filters
     *
     * @return array
     */
    public function paginateModels($filters = [])
    {
        $container = $this->getConfigurationPool()->getContainer();
        $filters['actual'] = true;
        $models = $container->get('doctrine')
            ->getRepository("AppBundle:ProductModels")
            ->getAdminSearchQuery($filters);

        $models = $container->get('knp_paginator')->paginate(
            $models,
            Arr::get($filters, 'page', 1),
            $this->sizesPerPage,
            ['wrap-queries' => true]
        );

        return $models;
    }

    /**
     * @param array $newFilters
     *
     * @return array
     */
    public function paramsToGetSizes($newFilters)
    {
        $parameters = array_merge($this->request->request->all(), $newFilters);

        return json_encode($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $object = parent::getNewInstance();

        return $this->setDefaults($object);
    }

    /**
     * {@inheritdoc}
     */
    public function getObject($id)
    {
        $object = parent::getObject($id);

        return $this->setDefaults($object);
    }

    /**
     * @param $object
     */
    protected function setDefaults($object)
    {
        if (!$object->getAdditionalSolarDescription()) {
            $object->setAdditionalSolarDescription('Доставка');
        }

        return $object;
    }

    /**
     * @return \Closure
     */
    protected function getStatusQuery()
    {
        return function (EntityRepository $er) {
            $status = $this->getSubject()->getStatus();

            return $er->createQueryBuilder('s')
                ->addOrderBy('FIELD(s.code, \'canceled\')', 'DESC')
                ->addOrderBy('s.priority', 'ASC')
                ->where('s.priority >= :priority')
                ->orWhere('s.code = :code')
                ->setParameter('code', 'canceled')
                ->setParameter('priority', $status ? $status->getPriority() : null)->setMaxResults(3);
        };
    }

    public function getHistoryItemLabel(OrderHistory $historyItem)
    {
        $historyManager = $this->get('history_manager');
        $history = $historyManager->createFromHistoryItem($historyItem);

        return $history->label();
    }

    /**
     * @param $callback
     *
     * @return Orders
     */
    public function createFromCallback($callback)
    {
        $em = $this->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $callback = $em->getRepository('AppBundle:CallBack')->find($callback);

        $order = new Orders();
        $order->setPhone($callback->getPhone());
        $order->setCarriers($em->getRepository('AppBundle:Carriers')->findOneById(1));
        $order->setType(Orders::TYPE_QUICK);
        $order->setStatus($em->getRepository('AppBundle:OrderStatus')->findOneBy(['code' => 'new']));

        $em->persist($order);
        $em->flush();

        return $order;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
//        payStatus
        $errorElement
            ->with('payStatus')
            ->assertNotNull(array())
            ->assertNotBlank()
            ->end()
            ->with('status')
            ->assertNotNull(array())
            ->assertNotBlank()
            ->end();
    }

    /**
     * @param int $currentPage
     * @param int $perPage
     *
     * @return array
     */
    public function paginateOtherOrders($currentPage = 1, $perPage = 10)
    {
        if ($user = $this->getSubject()->getUsers()) {
            $otherOrdersQuery = $this->modelManager->getEntityManager('AppBundle:Orders')
                ->getRepository('AppBundle:Orders')
                ->otherOrdersByUser($user, $this->subject);

            return $this->get('knp_paginator')->paginate(
                $otherOrdersQuery, $currentPage, $perPage, ['wrap-queries' => true]
            );
        }

        return [];
    }

    public function getOtherOrdersTotalSum()
    {
        if ($user = $this->getSubject()->getUsers()) {
            return $this->modelManager->getEntityManager('AppBundle:Orders')
                ->getRepository('AppBundle:Orders')
                ->otherOrdersSum($user, $this->subject);
        }

        return 0;
    }

    public function getWholesalerCart()
    {
        $wholesalerCart = $this->get('admin.wholesaler_cart');
        $wholesalerCart->setOrder($this->subject);
        return $wholesalerCart;
    }

    /**
     * @return bool
     */
    public function canChangeSizes()
    {
        return $this->subject->getStatus()->getCode() == 'new'
            && (!$this->subject->getRelatedOrder()
                || $this->subject->getRelatedOrder()->getStatus()->getCode() == 'new');
    }

    /**
     * Always allow move sizes from order. And are only matched condition for pre-order.
     *
     * @param ProductModelSpecificSize $size
     *
     * @return bool
     */
    public function canChangeSpecificSizes(ProductModelSpecificSize $size)
    {
        return !$this->subject->getPreOrderFlag() || ($size->getPreOrderFlag() || $size->getQuantity() > 0);
    }

    /**
     * @param $name
     * @param array $parameters
     * @param bool $absolute
     *
     * @return string
     */
    public function generateStoreUrl($name, array $parameters = [], $absolute = false)
    {
        $arrayRoute = $this->routeGenerator->generateMenuUrl($this, $name, $parameters, $absolute);

        return $this->routeGenerator->generate($arrayRoute['route'], $arrayRoute['routeParameters'],
            $arrayRoute['routeAbsolute']);
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        $type = [
            Orders::TYPE_NORMAL => 'обычный',
            Orders::TYPE_QUICK => 'быстрый',
        ][$object->getType()];

        return "{$object->getIdentifier()} ($type)";
    }

    /**
     * @param $historyItem
     */
    protected function getSizeFromHistory($historyItem)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        return $em->getRepository('AppBundle:ProductModelSpecificSize')->find($historyItem->getAdditional('sizeId'));
    }

    /**
     * @param $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->getConfigurationPool()->getContainer()->get($id);
    }

    /**
     * @param $form
     * @param $carrier
     */
    protected function addCitiesField($form, $carrier)
    {
        $form->add('cities', 'entity', [
            'class' => 'AppBundle:Cities',
            'label' => 'Город',
            'required' => false,
            'read_only' => $this->disableEdit,
            'disabled' => $this->disableEdit,
            'query_builder' => function (EntityRepository $er) use ($carrier) {
                return $er->createQueryBuilder('c')
                    ->where('c.carriers = :id AND c.active = 1')
                    ->setParameter('id', $carrier)
                    ->orderBy('c.name', 'ASC');
            },
            'empty_value' => 'Выберите город',
            'attr' => [
                'class' => 'js-cities'
            ]
        ]);
    }

    /**
     * @param $form
     * @param $city
     */
    protected function addStoresField($form, $city)
    {
        $form->add('stores', 'sonata_stores_list', [
            'class' => 'AppBundle:Stores',
            'label' => 'Склад',
            'required' => false,
            'read_only' => $this->disableEdit,
            'disabled' => $this->disableEdit,
            'attr' => [
                'class' => 'js-stores'
            ],
            'query_builder' => function (EntityRepository $er) use ($city) {
                return $er->createQueryBuilder('s')
                    ->where('s.cities = :id AND s.active = 1')
                    ->setParameter('id', $city)
                    ->orderBy('s.name', 'ASC');
            }
        ]);
    }
}
