<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity as Entity;

class ProductsAdmin extends Admin
{

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
            ->add('article', null, ['label' => 'Артикул'])
            ->add('name', null, ['label' => 'Название модели'])
            ->add('content', null, ['label' => 'Описание модели'])
            ->add('active', null, ['label' => 'Активный'])
//            ->add('published', null, ['label' => 'Опубликован'])
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
            ->add('baseCategory.name', null, ['label' => 'Категория'])
            ->add('active', 'boolean', ['label' => 'Активный', 'editable' => true])
//            ->add('published', 'boolean', ['label' => 'Опубликован', 'editable' => true])
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
//            ->add('published', null, ['label' => 'Опубликован'])
            ->add('baseCategory', 'entity', [
                'class' => 'AppBundle:Categories',
                'property' => 'name',
            ])
            ->add('price', null, ['label' => 'Цена'])
            ->add('wholesalePrice', null, ['label' => 'Оптовая цена'])
            ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
            ->add('seoDescription', null, ['label' => 'СЕО описание'])
            ->add('seoKeywords', null, ['label' => 'СЕО кейворды'])
            ->end()
            ->end()
            ->tab('Характеристики')
            ->with('СharacteristicValues',
                [
                    'class' => 'col-md-12',
                ])
            ->add('characteristicValues',
                'sonata_type_model_autocomplete',
                [
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Значения характеристик',
                    'multiple' => true,
                    'property' => 'name',
                    'minimum_input_length' => 1
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
            ->add('name', null, ['label' => 'Название модели'])
            ->add('article', null, ['label' => 'Артикул'])
            ->add('content', null, ['label' => 'Описание модели'])
            ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
            ->add('seoDescription', null, ['label' => 'СЕО описание'])
            ->add('seoKeywords', null, ['label' => 'СЕО кейворды'])
            ->add('active', null, ['label' => 'Активный'])
//            ->add('published', null, ['label' => 'Опубликован'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения']);
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
        $this->postUpdate($product);
    }

    /**
     * @param mixed $product
     * @return void
     */
    public function postUpdate($product)
    {
        $this->container->get('proc')->runUpdateRelationship();
    }

}
