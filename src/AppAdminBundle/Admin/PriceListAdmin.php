<?php

namespace AppAdminBundle\Admin;


use AppBundle\Helper\Arr;
use Exporter\Source\IteratorSourceIterator;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Exporter\Source\DoctrineORMQuerySourceIterator;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
            ->add('model.products', null, ['label' => 'Модель'])
            ->add('model.products.baseCategory', null, ['label' => 'Категория'])
            ->add('model.productColors', null, ['label' => 'Цвет'])
            ->add('size', null, ['label' => 'Размер'])
            ->add('model.products.article', null, ['label' => 'Артикул'])
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
            ->add('model.quantity', null, ['label' => 'Количество товара'])
            ->add('size.size', 'text', ['label' => 'Размер'])
            ->add('price', 'string', ['label' => 'Цена размера', 'editable' => true])
            ->add('wholesalePrice', 'string', ['label' => 'Оптовая цена размера', 'editable' => true])
            ->add('preOrderFlag', null, ['label' => 'Предзаказ', 'editable' => true])
            ->add('quantity', null, ['label' => 'Количество размера', 'editable' => true]);
    }

    public function getExportFields()
    {
        $exportFields['Категория'] = 'model.products.baseCategory.name';
        $exportFields['Модель'] = 'model.products.name';
        $exportFields['Цвет'] = 'model.productColors.name';
        $exportFields['Цвет отдели'] = 'model.decorationColor.name';
        $exportFields['Артикул'] = 'model.products.article';
        $exportFields['Размер'] = 'size.size';
        $exportFields['Цена размера'] = 'price';
        $exportFields['Оптовая цена размера'] = 'wholesalePrice';
        $exportFields['Предзаказ'] = 'preOrderFlag';
        $exportFields['Количество размера'] = 'quantity';
        $exportFields['Активность'] = 'model.products.active';
        $exportFields['Продукт опубликован'] = 'model.published';

        $exportFields['alias'] = 'model.alias';
        $exportFields['categoryAlias'] = 'model.products.baseCategory.alias';

        return $exportFields;
    }

    public function getExportFormats()
    {
        return ['xls'];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSourceIterator()
    {
        $dataIterator = parent::getDataSourceIterator();

        return new IteratorSourceIterator($this->getFormattedDataSourceGenerator($dataIterator));
    }

    private function getFormattedDataSourceGenerator($dataIterator)
    {
        foreach ($dataIterator as $item) {
            $href = $this->getConfigurationPool()
                ->getContainer()
                ->get('router')
                ->generate('product', [
                    'category' => Arr::pull($item, 'categoryAlias'),
                    'product' => Arr::pull($item, 'alias')
                ], UrlGeneratorInterface::ABSOLUTE_URL);

            $item['Модель'] = "<a href=\"$href\">{$item['Модель']}</a>";
            yield $item;
        }
    }
}
