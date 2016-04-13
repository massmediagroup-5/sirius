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
//            ->add('id')
            ->add('sku', null, array('label' => 'Артикул'))
            ->add('name', null, array('label' => 'Наименование'))
            ->add('status', null, array('label' => 'Статус'))
            ->add('active', null, array('label' => 'Активность'))
            ->add('price', null, array('label' => 'Цена'))
            ->add('wholesale_price', null, array('label' => 'Оптовая цена'))
            ->add('quantity', null, array('label' => 'Количество'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего обновления'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->add('id')
            ->add('productModels', null, array('label' => 'Модель'))
            ->add('sku', null, array('label' => 'Артикул'))
            ->add('name', null, array('label' => 'Наименование'))
            ->add('status', null, ['label' => 'Статус','editable' => true])
            ->add('active', null, ['label' => 'Активность','editable' => true])
            ->add('price', null, array('label' => 'Цена'))
            ->add('wholesale_price', null, array('label' => 'Оптовая цена'))
            ->add('quantity', null, array('label' => 'Количество'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего обновления'))
            ->add('_action', 'actions', array(
                'actions' => array(
//                    'show' => array(),
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
                'label' => 'Модель',
                'class' => 'AppBundle\Entity\ProductModels',
                'choice_label' => function (ProductModels $model) {
                    return $model->getProductColors() ? "{$model->getName()} ({$model->getProductColors()->getName()})"
                        : $model->getName();
                }
            ])
            ->add('sku', null, array('label' => 'Артикул'))
            ->add('name', null, array('label' => 'Наименование'))
            ->add('status', null, array('label' => 'Статус'))
            ->add('active', null, array('label' => 'Активность'))
            ->add('price', null, array('label' => 'Цена'))
            ->add('wholesale_price', null, array('label' => 'Оптовая цена'))
            ->add('quantity', null, array('label' => 'Количество'))
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
//            ->add('id')
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
