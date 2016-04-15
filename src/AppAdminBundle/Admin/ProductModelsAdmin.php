<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class ProductModelsAdmin
 * @package AppAdminBundle\Admin
 */
class ProductModelsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('alias', null, ['label' => 'Ссылка'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('active', null, ['label' => 'Активная'])
            ->add('inStock', null, ['label' => 'Наличие на складе'])
            ->add('published', null, ['label' => 'Опубликовано'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('alias', null, ['label' => 'Ссылка'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('active', null, ['editable' => true, 'label' => 'Активная'])
            ->add('inStock', null, ['editable' => true, 'label' => 'Наличие на складе'])
            ->add('published', null, ['editable' => true, 'label' => 'Опубликовано'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
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
            ->tab('Продукт')
            ->with('Продукт', [
                'class' => 'col-md-12',
            ])
            ->add('alias', null, ['label' => 'Ссылка'])
            ->add('productColors', 'entity',
                [
                    'class' => 'AppBundle:ProductColors',
                    'property' => 'name',
                    'label' => 'Цвет модели',
                    'empty_value' => 'Выберите цвет модели'
                ]
            )
            ->add('decorationColor', 'entity',
                [
                    'class' => 'AppBundle:ProductColors',
                    'property' => 'name',
                    'label' => 'Цвет отделки',
                    'empty_value' => 'Выберите цвет отделки',
                    'required' => false
                ]
            )
            ->add('price', null, ['label' => 'Цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('active', null, ['label' => 'Активная'])
            ->add('inStock', null, ['label' => 'Наличие на складе'])
            ->add('published', null, ['label' => 'Опубликовано'])
            ->end()
            ->end()
            ->tab('Размеры')
            ->with('Размеры', ['class' => 'col-md-12'])
            ->add('sizes', 'sonata_type_collection', ['label' => 'Размеры', 'by_reference' => false], [
                    'edit' => 'inline',
                    'inline' => 'table',
                ]
            )
            ->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('alias', null, ['label' => 'Ссылка'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('active', null, ['label' => 'Активная'])
            ->add('inStock', null, ['label' => 'Наличие на складе'])
            ->add('published', null, ['label' => 'Опубликовано'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения']);
    }

    /**
     * @param mixed $model
     * @return void
     */
    public function preUpdate($model)
    {
        foreach ($model->getSizes() as $size) {
            $size->setModel($model);
        }
    }
}
