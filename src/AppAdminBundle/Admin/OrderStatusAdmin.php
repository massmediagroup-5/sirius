<?php

namespace AppAdminBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class OrderStatusAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'Название статуса'])
            ->add('description', null, ['label' => 'Описание статуса'])
            ->add('baseFlag', null, ['label' => 'Базовый'])
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
            ->add('name', null, ['label' => 'Название статуса'])
            ->add('description', null, ['label' => 'Описание статуса'])
            ->add('baseFlag', null, ['label' => 'Базовый'])
            ->add('priority', null, ['editable' => true, 'label' => 'Приоритет'])
            ->add('sendClient', null, ['editable' => true, 'label' => 'Отправлять клиенту смс'])
            ->add('sendClientEmail', null, ['editable' => true, 'label' => 'Отправлять клиенту Email'])
            ->add('sendManager', null, ['editable' => true, 'label' => 'Отправлять менеджеру смс'])
            ->add('active', null, ['editable' => true, 'label' => 'Активность(вкл/выкл)'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Название статуса'])
            ->add('description', null, ['label' => 'Описание статуса'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('sendClient', null, ['label' => 'Отправлять клиенту смс'])
            ->add('sendClientText', null, [
                'label' => 'Текст смс клиенту',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"',
            ])
            ->add('sendClientNightText', null, [
                'label' => 'Текст смс клиенту(ночь)',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"',
            ])
            ->add('sendClientEmail', null, ['label' => 'Отправлять клиенту Email'])
            ->add('sendClientEmailText', null, [
                'label' => 'Текст Email клиенту',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"',
            ])
            ->add('sendManager', null, ['label' => 'Отправлять менеджеру смс'])
            ->add('sendManagerText', null, [
                'label' => 'Текст смс менеджеру',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s". Для нового заказа подставляется время заказа',
            ])
            ->add('sendManagerEmail', null, ['label' => 'Отправлять менеджер Email'])
            ->add('sendManagerEmailText', null, [
                'label' => 'Текст Email менеджер',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"',
            ])
            ->add('active', null, ['label' => 'Активность(вкл/выкл)']);
    }
}
