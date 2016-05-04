<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Orders;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Arr;
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
            ->add('ajax_update', $this->getRouterIdParameter() . '/ajax_update', [], [], [
                'expose' => true
            ])
            ->add('get_sizes', $this->getRouterIdParameter() . '/get_sizes', [], [], [
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
            ]);
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
        if ($user = $this->subject->getUsers()) {
            $otherSizes = $this->modelManager->getEntityManager('AppBundle:OrderProductSize')
                ->getRepository('AppBundle:OrderProductSize')
                ->createQueryBuilder('orderedSize')
                ->innerJoin('orderedSize.size', 'size')
                ->innerJoin('orderedSize.order', 'sizeOrder')
                ->where('sizeOrder.users = :user_id AND sizeOrder.id <> :id')
                ->setParameter('user_id', $user->getId())
                ->setParameter('id', $this->subject->getId())
                ->getQuery()
                ->getResult();
        } else {
            $otherSizes = [];
        }

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
                    'label' => 'Сатус заказа',
                    'empty_value' => 'Выберите статус заказа',
                    'query_builder' => function (EntityRepository $er) {
                        $status = $this->getSubject()->getStatus();
                        return $er->createQueryBuilder('s')
                            ->addOrderBy('FIELD(s.code, \'canceled\')', 'DESC')
                            ->addOrderBy('s.priority', 'ASC')
                            ->where('s.priority >= :priority')
                            ->orWhere('s.code = :code')
                            ->setParameter('code', 'canceled')
                            ->setParameter('priority', $status ? $status->getPriority() : null)->setMaxResults(3);
                    }
                ]
            )
            ->add('payStatus', 'entity',
                [
                    'class' => 'AppBundle:OrderStatusPay',
                    'property' => 'name',
                    'label' => 'Сатус оплаты заказа',
                    'empty_value' => 'Выберите статус оплаты',
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
                    (string)Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                    (string)Orders::PAY_TYPE_COD => 'Наложеным платежом',
                ]
            ])
            ->add('cities', 'entity', [
                'class' => 'AppBundle:Cities',
                'label' => 'Город',
                'read_only' => $this->disableEdit,
                'disabled' => $this->disableEdit,
                'query_builder' => function (EntityRepository $er) {
                    $carrier = $this->getSubject()->getCarriers();
                    return $er->createQueryBuilder('s')
                        ->where('s.carriers = :id')
                        ->setParameter('id', $carrier ? $carrier->getId() : null);
                }
            ])
            ->add('stores', 'sonata_stores_list', [
                'class' => 'AppBundle:Stores',
                'label' => 'Склад',
                'read_only' => $this->disableEdit,
                'disabled' => $this->disableEdit,
                'query_builder' => function (EntityRepository $er) {
                    if (!$cityId = Arr::get($this->request->request->get($this->getUniqid()), 'cities')) {
                        $city = $this->getSubject()->getCities();
                        $cityId = $city ? $city->getId() : null;
                    }
                    return $er->createQueryBuilder('s')
                        ->where('s.cities = :id')
                        ->setParameter('id', $cityId);
                }
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
            ->end();

        if ($otherSizes) {
            $formMapper->tab('Другие заказы покупателя', [
                'tab_template' => 'AppAdminBundle:admin:order_other_sizes.html.twig',
                'otherSizes' => $otherSizes
            ])
                ->end();
        }
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
     * @param array $filters
     * @return array
     */
    public function paginateModels($filters = [])
    {
        $container = $this->getConfigurationPool()->getContainer();
        $models = $container->get('doctrine')
            ->getRepository("AppBundle:ProductModels")
            ->getAdminOrdersQuery($filters);

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
}
