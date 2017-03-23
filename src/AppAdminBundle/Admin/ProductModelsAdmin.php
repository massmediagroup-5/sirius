<?php

namespace AppAdminBundle\Admin;

use AppAdminBundle\Form\Type\SonataTypeModelsList;
use Cocur\Slugify\Slugify;
use Illuminate\Support\Collection;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class ProductModelsAdmin
 * @package AppAdminBundle\Admin
 */
class ProductModelsAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createTime',
    );

    /**
     * @var array
     */
    protected $formOptions = array(
        'validation_groups' => 'Default'
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('alias', null, ['label' => 'Ссылка'])
            ->add('products', null, ['label' => 'Модель'])
            ->add('products.article', null, ['label' => 'Артикул'])
            ->add('products.baseCategory', null, ['label' => 'Категория'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('createTime', 'doctrine_orm_datetime_not_strict', ['label' => 'Дата создания'])
            ->add('updateTime', 'doctrine_orm_datetime_not_strict', ['label' => 'Дата последнего изменения'])
            ->add('isEndCount', 'doctrine_orm_callback', [
                'label' => 'Товар скоро закончится',
                'callback' => function ($queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return;
                    }
                    $em = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
                    $builder = $em->getRepository('AppBundle:ProductModelSpecificSize')
                        ->createQueryBuilder('sizes')
                        ->select('SUM(sizes.quantity)')
                        ->where("sizes.model = $alias.id");

                    $queryBuilder->andWhere("({$builder->getDQL()}) < $alias.endCount");

                    return true;
                },
                'field_type' => 'checkbox',
            ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $this->setTemplate('ajax', 'AppAdminBundle::ajax_layout.html.twig');

        $listMapper
            ->addIdentifier('alias', null, [
                'label' => 'Ссылка',
                'template' => 'AppAdminBundle:list:product_model_alias.html.twig'
            ])
            ->add('products.article', null, ['label' => 'Артикул'])
            ->add('images', null, [
                'label' => 'Картинка',
                'template' => 'AppAdminBundle:list:product_model_image.html.twig'
            ])
            ->addIdentifier('products.name', null, ['label' => 'Модель'])
            ->add('productColors.name', null, ['label' => 'Цвет'])
            ->add('products.baseCategory.name', null, ['label' => 'Категория'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('published', null, ['editable' => true, 'label' => 'Опубликованно'])
            ->add('sizes', 'string',
                [
                    'label' => 'Доступные размеры',
                    'template' => 'AppAdminBundle:list:list.template.modelsizes.html.twig',
                    'admin_code' => 'app.admin.product_model_specific_size'
                ]
            )
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
            ->add('_action', 'actions', [
                'actions' => [
//                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                    'clone' => [
                        'template' => 'AppAdminBundle:CRUD:list__action_clone.html.twig'
                    ]
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('Товар')
            ->with('Товар', [
                'class' => 'col-md-12',
            ]);
        if ($this->getSubject()->getProducts()){
            $formMapper
                ->add('products.article', null, [
                    'label' => 'Артикул',
                    'disabled' => true
                ]);
        }
        $formMapper
            ->add('alias', null, ['label' => 'Ссылка', 'required' => false])
            ->add('textLabel', 'text', ['label' => 'Метка', 'required' => false])
            ->add('textLabelColor', 'text',
                ['label' => 'Цвет метки', 'required' => false, 'attr' => ['class' => 'js_color_picker']])
            ->add('products', 'sonata_type_model_list', ['label' => 'Модель'])
            ->add('productColors', 'sonata_type_model_list', ['label' => 'Цвет товара'])
            ->add('decorationColor', 'sonata_type_model_list', ['label' => 'Цвет отделки'])
            ->add('price', 'number', ['label' => 'Цена', 'precision' => 2])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('endCount', null, ['label' => 'Количество для "Этот товар скоро заканчится"'])
            ->add('published', null, ['label' => 'Опубликованно'])
            ->end()
            ->end()
            ->tab('Размеры')
                ->with('Размеры', ['class' => 'col-md-12'])
                    ->add('sizes', 'sonata_type_collection', ['label' => 'Размеры', 'by_reference' => false], [
                            'admin_code' => 'app.admin.product_model_specific_size',
                            'edit' => 'inline',
                            'inline' => 'table',
                        ]
                    )
                ->end()
            ->end()
            ->tab('Рекомендуем')
            ->with('Рекомендуем', ['class' => 'col-md-12'])
            ->add('recommended', 'sonata_type_models_list',
                [
                    'class' => 'AppBundle:ProductModels',
                    'model_manager' => $this->getModelManager()
                ]
            )
            ->end()
            ->end();
        if (!$this->hasParentFieldDescription()) {
            if ($this->getSubject()->getId()) {
                $formMapper->tab('Изображения товара')
                    ->with('Изображения товара', ['class' => 'col-md-12'])
                    ->add('images', 'sonata_type_collection',
                        ['label' => 'Изображения'], ['edit' => 'inline']
                    )
                    ->end()
                    ->end();
            }
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('alias', null, ['label' => 'Ссылка'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('active', null, ['label' => 'Активная'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения']);
    }

    /**
     * @param mixed $model
     * @return void
     */
    public function prePersist($model)
    {
        $this->preUpdate($model);
    }

    /**
     * @param mixed $model
     * @return void
     */
    public function preUpdate($model)
    {
        if (!$model->getAlias()) {
            if ($model->getProducts()) {
                $slugify = new Slugify();
                $alias = rand(1, 99999) . ' ' . $model->getProducts()->getName() . ' '
                    . $model->getProducts()->getArticle() . ' ' . $model->getProductColors()->getName();
                $alias = $slugify->slugify($alias);
                $model->setAlias($alias);
            }
        }

        foreach ($model->getSizes() as $size) {
            $size->setModel($model);
        }
        foreach ($model->getImages() as $image) {
            $image->setModel($model);
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter() . '/clone')
        ->add('cancel_product_model_change', $this->getRouterIdParameter() . '/cancel_product_model_change/{history_id}');
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(parent::getFormTheme(), ['AppAdminBundle:Form:sonata_type_models_list.html.twig']);
    }

    /**
     * @return mixed
     */
    public function getDataSourceIterator()
    {
        $datagrid = $this->getDatagrid();
        $datagrid->buildPager();

        return $this->getModelManager()->getDataSourceIterator($datagrid, $this->getExportFields());
    }

    public function getExportFields()
    {
        $exportFields["Ссылка"] = 'alias';
        $exportFields["Артикул"] = 'products.article';
        $exportFields["Модель"] = 'products.name';
        $exportFields["Цвет"] = 'productColors.name';
        $exportFields["Категория"] = 'products.baseCategory.name';
        $exportFields["Цена"] = 'price';
        $exportFields["Оптовая цена"] = 'wholesalePrice';
        $exportFields["Приоритет"] = 'priority';
        $exportFields["Опубликованно"] = 'published';
        return $exportFields;
    }

    public function getExportFormats()
    {
        return ['xls'];
    }

    public function getBatchActions()
    {
        // Not show batch actions when rendered select many modal
        if (in_array($this->formTheme, [SonataTypeModelsList::TEMPLATE])) {
            return [];
        }
        return parent::getBatchActions();
    }

    public function getHistoryItemLabel($historyItem)
    {
        $historyManager = $this->getConfigurationPool()->getContainer()->get('history_manager');
        $history        = $historyManager->createFromHistoryItem($historyItem);

        return $history->label();
    }

    public function getHistory()
    {
        $historyItems = new Collection($this->subject->getHistory() ? $this->subject->getHistory()->getValues() : null);

        foreach ($this->subject->getSizes() as $size) {
            $historyItems = $historyItems->merge($size->getHistory()->getValues());
        }

        return $historyItems->sort(function ($a, $b) {
            if ($a->getCreateTime() == $b->getCreateTime()) {
                return 0;
            }

            return ($a->getCreateTime() > $b->getCreateTime()) ? -1 : 1;
        });
    }
}
