<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class OrderStatusAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Название статуса'))
//            ->add('code', null, array('label' => 'Код'))
            ->add('description', null, array('label' => 'Описание статуса'))
            ->add('baseFlag', null, array('label' => 'Базовый'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('sendClient', null, array('label' => 'Отправлять клиенту смс'))
            ->add('sendManager', null, array('label' => 'Отправлять менеджеру смс'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, array('label' => 'Название статуса'))
//            ->add('code', null, array('label' => 'Код'))
            ->add('description', null, array('label' => 'Описание статуса'))
            ->add('baseFlag', null, array('label' => 'Базовый'))
            ->add('priority', null, array('editable' => true, 'label' => 'Приоритет'))
            ->add('sendClient', null, array('editable' => true, 'label' => 'Отправлять клиенту смс'))
            ->add('sendClientEmail', null, array('editable' => true, 'label' => 'Отправлять клиенту Email'))
            ->add('sendManager', null, array('editable' => true, 'label' => 'Отправлять менеджеру смс'))
            ->add('active', null, array('editable' => true, 'label' => 'Активность(вкл/выкл)'))
            ->add('_action', 'actions', array(
                'actions' => array(
//                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
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
            ->add('name', null, array('label' => 'Название статуса'))
//            ->add('code', null, array('label' => 'Код'))
            ->add('description', null, array('label' => 'Описание статуса'))
//            ->add('baseFlag', null, array('label' => 'Базовый'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('sendClient', null, array('label' => 'Отправлять клиенту смс'))
            ->add('sendClientText', null, array(
                'label' => 'Текст смс клиенту',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"'
            ))
            ->add('sendClientNightText', null, array(
                'label' => 'Текст смс клиенту(ночь)',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"'
            ))
            ->add('sendClientEmail', null, array('label' => 'Отправлять клиенту Email'))
            ->add('sendClientEmailText', null, array(
                'label' => 'Текст Email клиенту',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"'
            ))
            ->add('sendManager', null, array('label' => 'Отправлять менеджеру смс'))
            ->add('sendManagerText', null, array(
                'label' => 'Текст смс менеджеру',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s". Для нового заказа подставляется время заказа'
            ))
            ->add('sendManagerEmail', null, array('label' => 'Отправлять менеджер Email'))
            ->add('sendManagerEmailText', null, array(
                'label' => 'Текст Email менеджер',
                'help' => '*Для подстановки идентификатора заказа в текст сообщения используйте выражение "%s"'
            ))
            ->add('active', null, array('label' => 'Активность(вкл/выкл)'))
        ;
    }

}
