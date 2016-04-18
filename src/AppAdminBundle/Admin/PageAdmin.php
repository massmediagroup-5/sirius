<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PageAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', array('label' => 'Заглавие'))
            ->add('alias', 'text', array('label' => 'Ссылка'))
            ->add('description', 'text', array('label' => 'Описание страницы'))
            ->add('seo_title', 'text', array('label' => 'СЕО заглавие'))
            ->add('seo_description', 'text', array('label' => 'СЕО описание'))
            ->add('seo_keywords', 'text', array('label' => 'СЕО кейворды'))
            ->add('content', 'textarea', array('label' => 'Контент','attr' => array('class' => 'ckeditor'))) //if no type is specified, SonataAdminBundle tries to guess it
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Заглавие страницы'))
            ->add('alias', null, array('label' => 'Ссылка'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', 'text', array('label' => 'Заглавие страницы'))
            ->add('alias', null, array('label' => 'Ссылка'))
            ->add('description', null, array('label' => 'Описание'))
        ;
    }
}
