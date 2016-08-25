<?php


namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class PriceListAdmin extends Admin
{

    protected $baseRouteName = 'price_list';

    protected $baseRoutePattern = '/app/price-list';

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->remove('create')
            ->remove('edit')
            ->remove('delete');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('model.products.name', null, ['label' => 'Модель'])
            ->add('model.products.article', null, ['label' => 'Артикул'])
            ->add('model.products.baseCategory.name', '', ['label' => 'Категория'])
            ->add('model.productColors.name', null, ['label' => 'Цвет'])
            ->add('size.size', null, ['label' => 'Размер'])
            ->add('quantity', null, ['label' => 'Количество'])
            ->add('price', null, ['label' => 'Цена размера'])
            ->add('model.price', null, ['label' => 'Цена продукта'])
            ->add('model.wholesalePrice', null, ['label' => 'Оптовая цена продукта'])
            ->add('model.updateTime', null, ['label' => 'Дата последнего обновления']);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('model.products.name', null, ['label' => 'Модель'])
            ->add('model.products.active', null, ['label' => 'Активность', 'editable' => true])
            ->add('model.products.article', null, ['label' => 'Артикул'])
            ->add('model.products.baseCategory.name', null, ['label' => 'Категория'])
            ->add('model.products.price', 'string', ['label' => 'Цена модели'])
            ->add('model.products.wholesalePrice', 'string', ['label' => 'Оптовая цена модели'])
            ->add('model.productColors', null, ['label' => 'Цвет'])
            ->add('model.decorationColor.name', null, ['label' => 'Цвет отдели'])
            ->add('model.alias', null, ['label' => 'Алиас'])
            ->add('model.price', 'string', ['label' => 'Цена продукта'])
            ->add('model.wholesalePrice', 'string', ['label' => 'Оптовая цена продукта'])
            ->add('model.published', null, ['label' => 'Продукт опубликован'])
            ->add('model.quantity', null, ['label' => 'Количество модели'])
            ->add('size.size', 'text', ['label' => 'Размер'])
            ->add('price', 'string', ['label' => 'Цена размера', 'editable' => true])
            ->add('wholesalePrice', 'string', ['label' => 'Оптовая цена размера', 'editable' => true])
            ->add('preOrderFlag', null, ['label' => 'Предзаказ', 'editable' => true])
            ->add('quantity', null, ['label' => 'Количество размера', 'editable' => true]);
    }

    public function getExportFields()
    {
        $exportFields['Модель'] = 'model.products.name';
        $exportFields['Активность'] = 'model.products.active';
        $exportFields['Артикул'] = 'model.products.article';
        $exportFields['Категория'] = 'model.products.baseCategory.name';
        $exportFields['Цена модели'] = 'model.products.price';
        $exportFields['Оптовая цена модели'] = 'model.products.wholesalePrice';
        $exportFields['Цвет'] = 'model.productColors.name';
        $exportFields['Цвет отдели'] = 'model.decorationColor.name';
        $exportFields['Алиас'] = 'model.alias';
        $exportFields['Цена продукта'] = 'model.price';
        $exportFields['Оптовая цена продукта'] = 'model.wholesalePrice';
        $exportFields['Продукт опубликован'] = 'model.published';
        $exportFields['Количество модели'] = 'model.quantity';
        $exportFields['Размер'] = 'size.size';
        $exportFields['Цена размера'] = 'price';
        $exportFields['Оптовая цена размера'] = 'wholesalePrice';
        $exportFields['Предзаказ'] = 'preOrderFlag';
        $exportFields['Количество размера'] = 'quantity';

        return $exportFields;
    }

    public function getExportFormats()
    {
        return ['xls'];
    }
}
