<?php

namespace AppAdminBundle\Admin;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\History;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ReturnedSizes;
use AppBundle\Entity\ReturnHistory;
use AppBundle\Entity\ReturnProductHistory;
use AppBundle\Validator\OrderStatusConstraint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use NovaPoshta\ApiModels\InternetDocument;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
//use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class ReturnProductAdmin extends Admin
{
    /**
     * @var string
     */
    protected $statusName = 'new';

    /**
     * @var bool
     */
    protected $disableEdit = true;

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
                        ''                           => 'Не указанно',
                        (string) Orders::TYPE_NORMAL => 'Обычный',
                        (string) Orders::TYPE_QUICK  => 'Быстрый',
                    )
                )
            )
            ->add('order.pay', 'doctrine_orm_choice', array('label' => 'Способ оплаты'),
                'choice',
                array(
                    'choices' => array(
                        ''                                  => 'Не выбрано',
                        (string) Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                        (string) Orders::PAY_TYPE_COD       => 'Наложеным платежом',
                    )
                )
            )
            ->add('createdAt', null, ['label' => 'Время оформления']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => '№ Заявки на возврат'])
            ->addIdentifier('order.type', 'choice', [
                'label'   => 'Тип заказа',
                'route'   => ['name' => 'edit'],
                'choices' => [
                    ''                           => 'Не указанно',
                    (string) Orders::TYPE_NORMAL => 'Обычный',
                    (string) Orders::TYPE_QUICK  => 'Быстрый',
                ]
            ])
            ->add('order.id', null, ['label' => '№ Заказа'])
            ->addIdentifier('order.fio', null, ['label' => 'Ф.И.О.', 'route' => ['name' => 'edit']])
            ->addIdentifier('user.phone', null, ['label' => 'Телефон', 'route' => ['name' => 'edit']])
            ->addIdentifier('user.email', null, ['label' => 'Email', 'route' => ['name' => 'edit']])
            ->add('order.pay', 'choice', [
                'label'   => 'Способ оплаты',
                'choices' => [
                    ''                                  => 'Не выбрано',
                    (string) Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                    (string) Orders::PAY_TYPE_COD       => 'Наложеным платежом',
                ]
            ])
            ->add('createdAt', null, ['label' => 'Время оформления'])
            ->add('_action', 'actions', [
                'actions' => [
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
            ->add('order.status', 'entity',
                [
                    'class' => 'AppBundle:OrderStatus',
                    'property' => 'name',
                    'label' => 'Статус заказа',
                    'read_only' => $this->disableEdit,
                    'disabled'  => $this->disableEdit,
                ]
            )
            ->add('order.payStatus', 'entity',
                [
                    'class'       => 'AppBundle:OrderStatusPay',
                    'property'    => 'name',
                    'label'       => 'Статус оплаты заказа',
                    'read_only' => $this->disableEdit,
                    'disabled'  => $this->disableEdit,
                ]
            )
            ->add('order.type', 'choice', [
                'label'     => 'Тип заказа',
                'read_only' => true,
                'disabled'  => true,
                'choices'   => [
                    (string) Orders::TYPE_NORMAL => 'Обычный',
                    (string) Orders::TYPE_QUICK  => 'Быстрый',
                ]
            ])
            ->add('order.fio', null, [
                'label'     => 'Ф.И.О.',
                'read_only' => $this->disableEdit,
                'disabled'  => $this->disableEdit,
            ])
            ->add('order.phone', null, [
                'label'     => 'Телефон',
                'read_only' => $this->disableEdit,
                'disabled'  => $this->disableEdit,
            ])
            ->add('order.pay', 'choice', [
                'label'     => 'Способ оплаты',
                'read_only' => $this->disableEdit,
                'disabled'  => $this->disableEdit,
                'choices'   => [
                    ''                                  => 'Не выбрано',
                    (string) Orders::PAY_TYPE_BANK_CARD => 'На карту банка',
                    (string) Orders::PAY_TYPE_COD       => 'Наложеным платежом',
                ]
            ])
            ->add('order.carriers', 'entity', [
                'class'       => 'AppBundle:Carriers',
                'label'       => 'Служба доставки',
                'read_only'   => $this->disableEdit,
                'disabled'    => $this->disableEdit,
                'empty_value' => 'Выберите службу доставки',
            ])
            ->add('order.cities', 'entity', [
                'class'         => 'AppBundle:Cities',
                'label'         => 'Город',
                'read_only'     => $this->disableEdit,
                'disabled'      => $this->disableEdit,
                'empty_value'   => 'Выберите город',
            ])
            ->add('order.stores', 'sonata_stores_list', [
                'class'         => 'AppBundle:Stores',
                'label'         => 'Склад',
                'read_only'     => $this->disableEdit,
                'disabled'      => $this->disableEdit,
            ])
            ->add('order.customDelivery', null, [
                'label' => 'Адрес доставки',
                'read_only'     => $this->disableEdit,
                'disabled'      => $this->disableEdit,
            ])
            ->add('order.comment', null, [
                'label' => 'Коментарий к заказу',
                'read_only'     => $this->disableEdit,
                'disabled'      => $this->disableEdit,
            ])
            ->add('order.comment_admin', null, [
                'label' => 'Коментарий к заказу для администратора',
                'read_only'     => $this->disableEdit,
                'disabled'      => $this->disableEdit,
            ])
            ->add('return_description', 'text', [
                'label' => 'Причина возврата товара',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Статус заявки на возврат',
                'choices' => [
                    'Заявка принята' => 'Заявка принята',
                    'Заявка отклонена' => 'Заявка отклонена',
                    'Заявка отменена' => 'Заявка отменена',
                    'Заявка выполнена' => 'Заявка выполнена',
                ]
            ])
            ->end()
            ->end()
            ->tab('Список заказанных товаров', [
                'tab_template' => 'AppAdminBundle:admin:return_product_sizes.html.twig'
            ])
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('createdAt')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter() . '/clone')
            ->add('cancel_return_product_change', $this->getRouterIdParameter() . '/cancel_return_product_change/{history_id}')
            ->add('cancel_returned_sizes_change', $this->getRouterIdParameter() . '/cancel_returned_sizes_change/{history_id}');
    }

    public function paginateOtherOrders($currentPage = 1, $perPage = 10)
    {
        if ($user = $this->getSubject()->getUser()) {
            $otherOrdersQuery = $this->modelManager->getEntityManager('AppBundle:Orders')
                ->getRepository('AppBundle:Orders')
                ->otherOrdersByUser($user, $this->subject);

            return $this->getConfigurationPool()->getContainer()->get('knp_paginator')->paginate(
                $otherOrdersQuery, $currentPage, $perPage, ['wrap-queries' => true]
            );
        }

        return [];
    }

    public function preUpdate($object)
    {
        $em = $this->getConfigurationPool()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        foreach ($this->request->request->get('return_sizes', []) as $return_size){
            if(array_key_exists('return', $return_size)) {
                $record = $object->getReturnedSizes()->filter(function ($size) use ($return_size) {
                    return $size->getSize()->getId() == $return_size['return'];
                })->first();
                if (!$record) {
                    $returnedSizes = new ReturnedSizes();
                    $returnedSizes->setCount(0)
                        ->setSize($em->getReference('AppBundle:OrderProductSize', $return_size['return']))
                        ->setReturnProduct($this->getSubject());
                    $this->getSubject()->addReturnedSizes($returnedSizes);
                    $em->persist($returnedSizes);
                    $em->flush();
                }
            }
        }

        foreach ($this->request->request->all() as $key => $value) {
            if($key == 'return_sizes'){
                foreach ($value as $return_size){
                    if(array_key_exists('return', $return_size)) {
                        $orderProductSize = $em->getReference('AppBundle:OrderProductSize', $return_size['return']);
                        $record = $object->getReturnedSizes()->filter(function ($size) use ($return_size) {
                            return $size->getSize()->getId() == $return_size['return'];
                        })->first();
                        if($record){
                            $record->setCount($return_size['count']);
                        }
                        else {
                            $returnedSizes = new ReturnedSizes();
                            $returnedSizes->setCount($return_size['count'])
                                ->setSize($orderProductSize)
                                ->setReturnProduct($this->getSubject());
                            $this->getSubject()->addReturnedSizes($returnedSizes);
                        }
                    }
                }
            }
        }
    }

    public function getHistoryItemLabel(History $historyItem)
    {
        $historyManager = $this->getConfigurationPool()->getContainer()->get('history_manager');
        $history        = $historyManager->createFromHistoryItem($historyItem);

        return $history->label();
    }

    public function getReturnSizeBySize($size)
    {
       $returnedSizes = $this->getSubject()->getReturnedSizes()->filter(function ($returnedSize) use ($size) {
           return $returnedSize->getSize()->getId() == $size;
       });

        return $returnedSizes ? $returnedSizes->first() : false;
    }

    public function getReturnSizeBySizeCount($size)
    {
       $returnedSize = $this->getReturnSizeBySize($size);

        return $returnedSize ? $returnedSize->getCount() : 0;
    }

    public function getHistory()
    {
        $historyItems = new Collection($this->subject->getHistory()->getValues());

        foreach ($this->subject->getReturnedSizes() as $size) {

            $historyItems = $historyItems->merge($size->getHistory()->getValues());
        }

        return $historyItems->sort(function ($a, $b) {
            if ($a->getCreateTime() == $b->getCreateTime()) {
                return 0;
            }
            return ($a->getCreateTime() > $b->getCreateTime()) ? -1 : 1;
        });
    }
    public function validate(ErrorElement $errorElement, $object)
    {
        foreach ($this->request->request->all() as $key => $value) {
            if ($key == 'return_sizes') {
                foreach ($value as $return_size) {
                    if (array_key_exists('return', $return_size)) {
                        $size = $object->getOrder()->getSizes()->filter(function ($size) use ($return_size) {
                            return $size->getId() == $return_size['return'];
                        })->first();
                        if ($size->getQuantity() < (int)$return_size['count']) {
                            $errorElement
                                ->with('name')
                                ->addViolation('Возврат не может превышать количество заказанных товаров!')
                                ->end();
                        }
                    }
                }
            }
        }
    }
}
