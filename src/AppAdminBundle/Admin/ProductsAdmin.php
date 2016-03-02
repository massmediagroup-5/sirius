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
            ->add('importName', null, array('label' => 'Название товара'))
            ->add('active', null, array('label' => 'Активный'))
            ->add('published', null, array('label' => 'Опубликован'))
            //->add('createTime',
            //'doctrine_orm_datetime_range',
            //array(
            //'label' => 'Дата создания',
            //'input_type' => 'timestamp',
            //'field_type' => 'sonata_type_date_range_picker',
            //)
            //)
            //->add('updateTime', null, array('label' => 'Дата последнего изменения'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('importName', null, array('label' => 'Название товара'))
            ->add('active', 'boolean', array('label' => 'Активный', 'editable' => true))
            ->add('published', 'boolean', array('label' => 'Опубликован', 'editable' => true))
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

        $id = $this->id($this->getSubject());

        $formMapper
            ->tab('Товар')
            ->with('Product',
                array(
                    'class' => 'col-md-12',
                ))
            ->add('importName', null, array('label' => 'Название товара'))
            ->add('active', null, array('label' => 'Активный'))
            ->add('published', null, array('label' => 'Опубликован'))
            ->add('baseCategory', 'entity', array(
                'class' => 'AppBundle:Categories',
                'property' => 'name',
            ))
            ->end()
            ->end()
            ->tab('Модели')
            ->with('ProductModels',
                array(
                    'class' => 'col-md-12',
                ))
            ->add('productModels', 'sonata_type_collection',
                array('label' => 'Модель'), array(
                    'edit' => 'inline',
                    //'inline' => 'table',
                    //'sortable'  => 'id'
                )
            //array(
            //'class'     => 'AppBundle:ProductModels',
            //'label'     => 'Модели',
            ////'expanded' => true,
            //'multiple' => true,
            //'by_reference' => true,
            //)
            )
            ->end()
            ->end()
            ->tab('Характеристики')
            ->with('СharacteristicValues',
                array(
                    'class' => 'col-md-12',
                ))
            ->add('characteristicValues',
                'sonata_type_model_autocomplete',
                array(
                    'attr' => array('class' => 'form-control'),
                    'label' => 'Значения характеристик',
                    'multiple' => true,
                    'property' => 'name',
                    'minimum_input_length' => 1
                )
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
            ->add('importName', null, array('label' => 'Название товара'))
            ->add('active', null, array('label' => 'Активный'))
            ->add('published', null, array('label' => 'Опубликован'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'));
    }

    /**
     * prePersist
     *
     * @param mixed $product
     */
    public function prePersist($product)
    {
        $this->preUpdate($product);
        $this->container->get('entities')->setActionLabels($product);
    }

    /**
     * preUpdate
     *
     * @param mixed $product
     */
    public function preUpdate($product)
    {
        $DM = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();

        foreach ($product->getProductModels() as $productModel) {
            // If SKU didn't set create new.
            if (count($productModel->getSkuProducts()) === 0) {
                $newSkuProduct = new Entity\SkuProducts;
                $defaultVendor = $DM->getRepository('AppBundle:Vendors')
                    ->findOneByName('none');
                $newSkuProduct
                    ->setProductModels($productModel)
                    ->setVendors($defaultVendor)
                    ->setSku(md5(time()))
                    ->setName($productModel->getName())
                    ->setPrice($productModel->getPrice())
                    ->setActive(1)
                    ->setQuantity(1);
                $productModel->addSkuProduct($newSkuProduct);
            }
        }
    }

    /**
     * postPersist
     *
     * @param mixed $product
     */
    public function postPersist($product)
    {
        $this->postUpdate($product);
    }

    /**
     * postUpdate
     *
     * @param mixed $product
     */
    public function postUpdate($product)
    {
        $this->container->get('proc')->runUpdateRelationship();
    }

}
