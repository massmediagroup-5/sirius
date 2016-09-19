<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity as Entity;
use Sonata\AdminBundle\Route\RouteCollection;

class ProductsAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createTime',
    );

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * container
     *
     * @var mixed
     */
    private $container = null;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, $container = null)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
        $this->em = $container->get('Doctrine')->getManager();
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('baseCategory', null, ['label' => 'Категория'])
            ->add('article', null, ['label' => 'Артикул'])
//            ->add('name', null, ['label' => 'Название модели'])
            ->add('name', 'doctrine_orm_callback',
                [
                    'label'       => 'Название модели',
                    'show_filter' => true,
                    'callback'    => function ($queryBuilder, $alias, $field, $value) {
                        if ( ! $value['value']) {
                            return false;
                        }
                        $queryBuilder
                            ->andWhere($alias . '.name LIKE :value')
                            ->setParameter('value', '%' . $value['value']->getName() . '%');

                        return true;
                    },
                ],
                'entity',
                [
                    'class'         => 'AppBundle:Products',
                    'property'      => 'name',
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('o');
                            $qb->select('o')->groupBy('o.name');

                            return $qb;
                        }
                ]
            )
            ->add('content', null, ['label' => 'Описание модели'])
            ->add('active', null, ['label' => 'Активный'])
            ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
            ->add('seoDescription', null, ['label' => 'СЕО описание'])
            ->add('seoKeywords', null, ['label' => 'СЕО кейворды']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, ['label' => 'Название модели'])
            ->add('article', null, ['label' => 'Артикул'])
            ->add('productModels', null, [
                'label' => 'Количество товаров',
                'template' => 'AppAdminBundle:list:product_models_count.html.twig'
            ])
            ->add('baseCategory.name', null, ['label' => 'Категория'])
            ->add('active', 'boolean', ['label' => 'Активный', 'editable' => true])
            ->add('characteristicValues', null, ['label' => 'Значения характеристик'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
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
            ->tab('Модель')
            ->with('Модель',
                [
                    'class' => 'col-md-12',
                ])
            ->add('name', null, ['label' => 'Название модели'])
            ->add('article', null, ['label' => 'Артикул'])
            ->add('content', null, ['label' => 'О модели', 'attr' => ['class' => 'ckeditor']])
            ->add('characteristics', null, ['label' => 'Характеристики', 'attr' => ['class' => 'ckeditor']])
            ->add('features', null, ['label' => 'Особенности', 'attr' => ['class' => 'ckeditor']])
            ->add('active', null, ['label' => 'Активный'])
            ->add('baseCategory', 'sonata_type_model',[],['admin_code' => 'app.admin.categories'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
            ->add('seoDescription', null, ['label' => 'СЕО описание'])
            ->add('seoKeywords', null, ['label' => 'СЕО кейворды'])
            ->end()
            ->end()
            ->tab('Характеристики')
            ->with('Характеристики модели',
                [
                    'class' => 'col-md-12',
                ])
            ->add('characteristicValues',
                'entity',
                [
                    'class'    => 'AppBundle:CharacteristicValues',
                    'label' => 'Значения характеристик',
                    'expanded' => true,
                    'multiple' => true,
                    'by_reference' => false
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
            ->tab('Модель')
            ->with('Модель',
                [
                    'class' => 'col-md-12',
                ])
            ->add('name', null, ['label' => 'Название модели'])
            ->add('article', null, ['label' => 'Артикул'])
            ->add('content', null, ['label' => 'О модели', 'attr' => ['class' => 'ckeditor']])
            ->add('characteristics', null, ['label' => 'Характеристики', 'attr' => ['class' => 'ckeditor']])
            ->add('features', null, ['label' => 'Особенности', 'attr' => ['class' => 'ckeditor']])
            ->add('active', null, ['label' => 'Активный'])
            ->add('baseCategory', 'sonata_type_model',[],['admin_code' => 'app.admin.categories'])
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
            ->add('seoDescription', null, ['label' => 'СЕО описание'])
            ->add('seoKeywords', null, ['label' => 'СЕО кейворды'])
            ->end()
            ->end()
            ->tab('Характеристики')
            ->with('Характеристики модели',
                [
                    'class' => 'col-md-12',
                ])
            ->add('characteristicValues',
                'entity',
                [
                    'class'    => 'AppBundle:CharacteristicValues',
                    'label' => 'Значения характеристик',
                    'expanded' => true,
                    'multiple' => true,
                    'by_reference' => false
                ]
            )
            ->end()
            ->end();
    }

    /**
     * @param mixed $product
     * @return void
     */
    public function prePersist($product)
    {
        $this->preUpdate($product);
        $this->container->get('entities')->setActionLabels($product);
    }

    /**
     * @param mixed $product
     * @return void
     */
    public function postPersist($product)
    {
//        $this->postUpdate($product);
    }

    /**
     * @param mixed $product
     * @return void
     */
    public function postUpdate($product)
    {
//        $this->container->get('proc')->runUpdateRelationship();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
    }

    public function getExportFields()
    {
        $exportFields["Название модели"] = 'name';
        $exportFields["Артикул"] = 'article';
        $exportFields["Модель"] = 'products.name';
        $exportFields["Категория"] = 'baseCategory.name';
        return $exportFields;
    }

    public function getExportFormats()
    {
        return ['xls'];
    }
}
