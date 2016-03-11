<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\ProductModels;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SkuProductsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('sku')
            ->add('name')
            ->add('status')
            ->add('active')
            ->add('price')
            ->add('wholesale_price')
            ->add('quantity')
            ->add('createTime')
            ->add('updateTime');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('productModels')
            ->add('sku')
            ->add('name')
            ->add('status', null, ['editable' => true])
            ->add('active', null, ['editable' => true])
            ->add('price')
            ->add('wholesale_price')
            ->add('quantity')
            ->add('createTime')
            ->add('updateTime')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            //->add('id')
            ->add('productModels', 'entity', [
                'class' => 'AppBundle\Entity\ProductModels',
                'choice_label' => function (ProductModels $model) {
                    return $model->getProductColors() ? "{$model->getName()} ({$model->getProductColors()->getName()})"
                        : $model->getName();
                }
            ])
            ->add('sku')
            ->add('name')
            ->add('status')
            ->add('active')
            ->add('price')
            ->add('wholesale_price')
            ->add('quantity')
            //->add('createTime')
            //->add('updateTime')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('sku')
            ->add('name')
            ->add('status')
            ->add('active')
            ->add('price')
            ->add('wholesale_price')
            ->add('quantity')
            ->add('createTime')
            ->add('updateTime');
    }

    /**
     * undocumented function
     *
     * @return void
     */
    public function preUpdate($param)
    {
    }

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();

        $actions['activate'] = [
            'label' => $this->trans('list.action_activate', array(), 'AppAdminBundle'),
            'ask_confirmation' => true // If true, a confirmation will be asked before performing the action
        ];

        $actions['deactivate'] = [
            'label' => $this->trans('list.action_deactivate', array(), 'AppAdminBundle'),
            'ask_confirmation' => true
        ];

        return $actions;
    }
}
