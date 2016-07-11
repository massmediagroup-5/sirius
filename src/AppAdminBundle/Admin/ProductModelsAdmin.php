<?php

namespace AppAdminBundle\Admin;

use Cocur\Slugify\Slugify;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;


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
            ->add('products.baseCategory', null, ['label' => 'Категория'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('alias', null, [
                'label' => 'Ссылка',
                'template' => 'AppAdminBundle:list:product_model_alias.html.twig'
            ])
            ->add('products.article', null, ['label' => 'Артикул'])
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
            ])
            ->add('alias', null, ['label' => 'Ссылка', 'required' => false])
            ->add('products', 'sonata_type_model_list', ['label' => 'Модель'])
            ->add('productColors', 'sonata_type_model_list', ['label' => 'Цвет товара', ])
            ->add('decorationColor', 'sonata_type_model_list', ['label' => 'Цвет отделки'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('oldPrice', null, ['label' => 'Старая цена'])
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
                    ->add('recommended', 'sonata_type_model',
                        [
                            'by_reference' => true,
                            'multiple' => true,
                            'required' => false,
                            'btn_add' => false
                        ]
                    )
                ->end()
            ->end();
        if (!$this->hasParentFieldDescription()) {
            if($this->getSubject()->getId()){
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
    public function prePersist($model) {
        $this->preUpdate($model);
    }

    /**
     * @param mixed $model
     * @return void
     */
    public function preUpdate($model)
    {
        if(!$model->getAlias()) {
            $slugify = new Slugify();
            $alias = rand(1,99999) . ' ' . $model->getProducts()->getName() . ' ' . $model->getProducts()->getArticle() . ' ' . $model->getProductColors()->getName();
            $alias = $slugify->slugify($alias);
            $model->setAlias($alias);
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
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
    }
}
