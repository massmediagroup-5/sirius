<?php


namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PriceListAdmin extends Admin
{

    protected $baseRouteName = 'price_list';

    protected $baseRoutePattern = '/app/price-list';

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('model.products.name', null, ['label' => 'Модель'])
            ->add('model.products.article', null, ['label' => 'Артикул'])
            ->add('model.products.baseCategory.name', null, ['label' => 'Категория'])
            ->add('model.productColors.name', null, ['label' => 'Цвет'])
            ->add('size.size', 'text', ['label' => 'Размер'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('price', null, ['label' => 'Цена размера'])
            ->add('model.price', null, ['label' => 'Цена продукта'])
            ->add('model.wholesalePrice', null, ['label' => 'Оптовая цена продукта'])
            ->add('model.updateTime', null, ['label' => 'Дата последнего обновления'])
        ;
    }

    public function getExportFields()
    {
        $exportFields["Артикул"] = 'model.products.article';
        $exportFields["Категория"] = 'model.products.baseCategory.name';
        $exportFields["Цвет"] = 'model.productColors';
        $exportFields["Размер"] = 'size.size';
        $exportFields["Количество"] = 'quantity';
        $exportFields["Цена размера"] = 'price';
        $exportFields["Цена продукта"] = 'model.price';
        $exportFields["Дата последнего обновления"] = 'model.updateTime';
        return $exportFields;
    }

    public function getExportFormats()
    {
        return array(
            'xls'
        );
    }
}