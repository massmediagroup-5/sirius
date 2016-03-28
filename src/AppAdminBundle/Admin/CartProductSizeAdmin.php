<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Orders;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;


class CartProductSizeAdmin extends Admin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete');

    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('cart', null, ['label' => 'Корзина'])
            ->add('size', null, ['label' => 'Размер']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('cart', null, ['label' => 'Корзина'])
            ->add('size', null, ['label' => 'Размер']);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('cart', null, ['label' => 'Корзина'])
            ->add('size', null, ['label' => 'Размер']);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('cart', null, ['label' => 'Корзина'])
            ->add('size', null, ['label' => 'Размер']);
    }
}
