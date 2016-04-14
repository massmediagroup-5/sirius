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
            ->add('name', null, array('label' => 'Название модели'))
            ->add('description', null, array('label' => 'Описание модели'))
            ->add('alias', null, array('label' => 'Ссылка'))
            ->add('price', null, array('label' => 'Цена'))
            ->add('seoTitle', null, array('label' => 'СЕО заглавие'))
            ->add('seoDescription', null, array('label' => 'СЕО описание'))
            ->add('seoKeywords', null, array('label' => 'СЕО кейворды'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('active', null, array('label' => 'Активная'))
            ->add('inStock', null, array('label' => 'Наличие на складе'))
            ->add('published', null, array('label' => 'Опубликовано'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'Название модели'))
            //->add('description', null, array('label' => 'Описание модели'))
            ->addIdentifier('alias', null, array('label' => 'Ссылка'))
            ->add('price', null, array('label' => 'Цена'))
            //->add('seoTitle', null, array('label' => 'СЕО заглавие'))
            //->add('seoDescription', null, array('label' => 'СЕО описание'))
            //->add('seoKeywords', null, array('label' => 'СЕО кейворды'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('active', null, ['editable' => true, 'label' => 'Активная'])
            ->add('inStock', null, ['editable' => true, 'label' => 'Наличие на складе'])
            ->add('published', null, ['editable' => true, 'label' => 'Опубликовано'])
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'))
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
            ->tab('Модель')
            ->with('ProductModels',
                array(
                    'class' => 'col-md-12',
                ));
        if (!$this->hasParentFieldDescription()) {
            $formMapper->add('products', 'entity', [
                'class' => 'AppBundle\Entity\Products',
                'label' => 'Продукт'
            ]);
        }

        $formMapper->add('name', null, array('label' => 'Название модели'))
            ->add('content', null, array('label' => 'О модели', 'attr' => array('class' => 'ckeditor')))
            ->add('characteristics', null, array('label' => 'Характеристики', 'attr' => array('class' => 'ckeditor')))
            ->add('features', null, array('label' => 'Особенности', 'attr' => array('class' => 'ckeditor')))
            ->add('alias', null, array('label' => 'Ссылка'))
            ->add('productColors', 'entity',
                array(
                    'class' => 'AppBundle:ProductColors',
                    'property' => 'name',
                    'label' => 'Цвет модели',
                    'empty_value' => 'Выберите цвет модели'
                )
            )
            ->add('sizes', 'entity',
                array(
                    'class' => 'AppBundle:ProductModelSizes',
                    'multiple' => true,
                    'property' => 'size',
                    'label' => 'Размер',
                    'empty_value' => 'Выберите размер модели'
                )
            )
            ->add('decorationColor', 'entity',
                array(
                    'class' => 'AppBundle:ProductColors',
                    'property' => 'name',
                    'label' => 'Цвет отделки',
                    'empty_value' => 'Выберите цвет отделки',
                    'required' => false
                )
            )
            ->add('price', null, array('label' => 'Цена'))
            ->add('seoTitle', null, array('label' => 'СЕО заглавие'))
            ->add('seoDescription', null, array('label' => 'СЕО описание'))
            ->add('seoKeywords', null, array('label' => 'СЕО кейворды'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('active', null, array('label' => 'Активная'))
            ->add('inStock', null, array('label' => 'Наличие на складе'))
            ->add('published', null, array('label' => 'Опубликовано'))
            ->add('preOrderFlag', null, array('label' => 'Предзаказ'))
            ->end()
            ->end();
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, array('label' => 'Название модели'))
            ->add('description', null, array('label' => 'Описание модели'))
            ->add('alias', null, array('label' => 'Ссылка'))
            ->add('price', null, array('label' => 'Цена'))
            ->add('seoTitle', null, array('label' => 'СЕО заглавие'))
            ->add('seoDescription', null, array('label' => 'СЕО описание'))
            ->add('seoKeywords', null, array('label' => 'СЕО кейворды'))
            ->add('priority', null, array('label' => 'Приоритет'))
            ->add('active', null, array('label' => 'Активная'))
            ->add('inStock', null, array('label' => 'Наличие на складе'))
            ->add('published', null, array('label' => 'Опубликовано'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'));
    }
}
