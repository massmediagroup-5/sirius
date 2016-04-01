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
            ->add('title', 'text', array('label' => 'Post Title'))
            ->add('alias', 'text', array('label' => 'Alias'))
            ->add('description', 'text', array('label' => 'Description'))
            ->add('seo_title', 'text', array('label' => 'SEO Title'))
            ->add('seo_description', 'text', array('label' => 'SEO Description'))
            ->add('seo_keywords', 'text', array('label' => 'SEO Keywords'))
            ->add('content', 'textarea', array('label' => 'Контент','attr' => array('class' => 'ckeditor'))) //if no type is specified, SonataAdminBundle tries to guess it
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('alias')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('alias')
            ->add('description')
        ;
    }
}
