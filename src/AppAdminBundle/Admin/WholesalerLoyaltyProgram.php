<?php

namespace AppAdminBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class WholesalerLoyaltyProgram
 * @package AppAdminBundle\Admin
 */
class WholesalerLoyaltyProgram extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('sumFrom', null, ['label' => 'Сума от'])
            ->add('sumTo', null, ['label' => 'Сума до'])
            ->add('discount', null, ['label' => 'Скидка']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('sumFrom', null, ['label' => 'Сума от'])
            ->add('sumTo', null, ['label' => 'Сума до'])
            ->add('discount', null, ['label' => 'Скидка'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Програма лояльности')
            ->with('Програма лояльности', ['class' => 'col-md-12'])
            ->add('sumFrom', null, ['label' => 'Сума от'])
            ->add('sumTo', null, ['label' => 'Сума до'])
            ->add('discount', null, ['label' => 'Скидка'])
            ->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('sumFrom', null, ['label' => 'Сума от'])
            ->add('sumTo', null, ['label' => 'Сума до'])
            ->add('discount', null, ['label' => 'Скидка']);
    }

}
