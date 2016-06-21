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
            ->add('title', 'text', array('label' => 'Название страницы'))
            ->add('alias', 'text', array('label' => 'Ссылка'))
            ->add('description', 'text', array('label' => 'Описание страницы'))
            ->add('content', 'textarea', array('label' => 'Контент','attr' => array('class' => 'ckeditor')))
            ->add('seo_title', 'text', array('label' => 'META title'))
            ->add('seo_description', 'text', array('label' => 'META description'))
            ->add('seo_keywords', 'text', array('label' => 'META keywords'))
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
