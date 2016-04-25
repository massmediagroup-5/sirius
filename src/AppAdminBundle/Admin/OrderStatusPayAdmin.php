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
            ->add('name', null, array('label' => 'Название статуса оплаты'))
//            ->add('code', null, array('label' => 'Код'))
            ->add('description', null, array('label' => 'Описание статуса оплаты'))
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
            ->add('name', null, array('label' => 'Название статуса оплаты'))
//            ->add('code', null, array('label' => 'Код'))
            ->add('description', null, array('label' => 'Описание статуса оплаты'))
            ->add('baseFlag', null, array('label' => 'Базовый'))
            ->add('priority', null, array('editable' => true, 'label' => 'Приоритет'))
            ->add('sendClient', null, array('editable' => true, 'label' => 'Отправлять клиенту смс'))
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
            ->add('name', null, array('label' => 'Название статуса оплаты'))
//            ->add('code', null, array('label' => 'Код'))
            ->add('description', null, array('label' => 'Описание статуса оплаты'))
//            ->add('baseFlag', null, array('label' => 'Базовый'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('sendClient', null, array('label' => 'Отправлять клиенту смс'))
            ->add('sendClientText', null, array('label' => 'Текст смс клиенту'))
            ->add('sendManager', null, array('label' => 'Отправлять менеджеру смс'))
            ->add('sendManagerText', null, array('label' => 'Текст смс менеджеру'))
            ->add('active', null, array('label' => 'Активность(вкл/выкл)'))
        ;
    }

}
