<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class: OrderProductSizeAdmin
 *
 * @see Admin
 */
class OrderProductSizeAdmin extends Admin
{

    /**
     * @var string
     */
    protected $parentAssociationMapping = 'order';

    /**
     * configureFormFields
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('size.model.products.article', null, ['label' => 'Размер'])
            ->add('size.model.products.name', null, ['label' => 'Размер'])
            ->add('size.model.productColors.name', null, ['label' => 'Размер'])
            ->add('size.size', null, ['label' => 'Размер'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('totalPrice', null, ['label' => 'Цена', 'required' => true])
            ->add('discountedTotalPrice', null, ['label' => 'Цена со скидкой']);
    }

    /**
     * configureShowFields
     *
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('size.size', null, ['label' => 'Размер'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('totalPrice', null, ['label' => 'Цена', 'required' => true])
            ->add('discountedTotalPrice', null, ['label' => 'Цена со скидкой']);
    }
}
