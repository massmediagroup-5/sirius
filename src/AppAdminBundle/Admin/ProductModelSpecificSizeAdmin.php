<?php

namespace AppAdminBundle\Admin;

use AppBundle\Validator\ProductPriceConstraint;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class: ProductModelSpecificSizeAdmin
 *
 * @see Admin
 */
class ProductModelSpecificSizeAdmin extends Admin
{

    /**
     * @var string
     */
    protected $parentAssociationMapping = 'models';

    /**
     * configureDatagridFilters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('preOrderFlag', null, ['label' => 'Предзаказ']);
    }

    /**
     * configureListFields
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('preOrderFlag', null, ['label' => 'Предзаказ'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * configureFormFields
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('price', null, ['label' => 'Цена',
                'required' => true,
                'constraints' => [new ProductPriceConstraint()],
                'precision' => 2
            ])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('preOrderFlag', null, ['label' => 'Предзаказ'])
            ->add('size', 'entity', [
                    'class' => 'AppBundle:ProductModelSizes',
                    'property' => 'size',
                    'label' => 'Размер',
                    'empty_value' => 'Выберите размер',
                    'required' => true
                ]
            );
    }

    /**
     * configureShowFields
     *
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('preOrderFlag', null, ['label' => 'Предзаказ']);
    }
}
