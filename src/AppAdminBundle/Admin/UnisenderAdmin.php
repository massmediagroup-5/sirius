<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UnisenderAdmin extends Admin
{

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('phones', null, ['label' => 'Номера телефонов'])
            ->add('apiKey', null, ['label' => 'Ключ API'])
            ->add('senderName', null, ['label' => 'Имя отправителя'])
            ->add('active', null, ['label' => 'Активность(вкл/выкл)', 'editable'=>true])
            ->add('_action', 'actions', array(
                'actions' => array(
//                    'show' => array(),
                    'edit' => array(),
//                    'delete' => array(),
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
            ->add('phones', null, ['label' => 'Номера телефонов(через запятую, без пробелов)'])
            ->add('apiKey', null, ['label' => 'Ключ API'])
            ->add('senderName', null, ['label' => 'Имя отправителя'])
            ->add('active', null, ['label' => 'Активность(вкл/выкл)'])
        ;
    }
}
