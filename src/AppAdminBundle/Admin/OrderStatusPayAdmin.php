<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class OrderStatusPayAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Название статуса оплаты'])
            ->add('code', null, ['label' => 'Код'])
            ->add('description', null, ['label' => 'Описание статуса оплаты'])
            ->add('baseFlag', null, ['label' => 'Базовый'], 'sonata_type_translatable_choice', [
                'translation_domain' => 'SonataAdminBundle',
                'choices' => [
                    1 => 'label_type_yes',
                    2 => 'label_type_no',
                ],
            ])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('sendClient', null, ['label' => 'Отправлять клиенту смс'], 'sonata_type_translatable_choice', [
                'translation_domain' => 'SonataAdminBundle',
                'choices' => [
                    1 => 'label_type_yes',
                    2 => 'label_type_no',
                ],
            ])
            ->add('sendManager', null, ['label' => 'Отправлять менеджеру смс'], 'sonata_type_translatable_choice', [
                'translation_domain' => 'SonataAdminBundle',
                'choices' => [
                    1 => 'label_type_yes',
                    2 => 'label_type_no',
                ],
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, ['label' => 'Название статуса оплаты'])
            ->add('code', null, ['label' => 'Код'])
            ->add('description', null, ['label' => 'Описание статуса оплаты'])
            ->add('baseFlag', null, ['label' => 'Базовый'])
            ->add('priority', null, ['editable' => true, 'label' => 'Приоритет'])
            ->add('sendClient', null, ['editable' => true, 'label' => 'Отправлять клиенту смс'])
            ->add('sendManager', null, ['editable' => true, 'label' => 'Отправлять менеджеру смс'])
            ->add('active', null, ['editable' => true, 'label' => 'Активность(вкл/выкл)'])
            ->add('_action', 'actions', [
                'actions' => [
//                    'show' => array(),
                    'edit' => [],
                    'delete' => [],
                ]
            ])
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Название статуса оплаты'])
            ->add('code', null, ['label' => 'Код'])
            ->add('description', null, ['label' => 'Описание статуса оплаты'])
//            ->add('baseFlag', null, array('label' => 'Базовый'))
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('sendClient', null, ['label' => 'Отправлять клиенту смс'])
            ->add('sendClientText', null, [
                'label' => 'Текст смс клиенту',
                'help' => 'Для подстановки идентификатора заказа в текст сообщения используйте выражение %s'
            ])
            ->add('sendClientNightText', null, [
                'label' => 'Текст смс клиенту(ночь)',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"'
            ])
            ->add('sendManager', null, ['label' => 'Отправлять менеджеру смс'])
            ->add('sendManagerText', null, [
                'label' => 'Текст смс менеджеру',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s". Для нового заказа подставляется время заказа'
            ])
            ->add('active', null, ['label' => 'Активность(вкл/выкл)'])
        ;
    }

}
