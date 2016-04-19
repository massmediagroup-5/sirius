<?php

namespace AppAdminBundle\Admin;

use Symfony\Component\DependencyInjection\ContainerAware;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class: ProductModelImageAdmin
 *
 * @see Admin
 */
class ProductModelImagesAdmin extends Admin
{

    /**
     * @var string
     */
    protected $parentAssociationMapping = 'model';

    /**
     * configureDatagridFilters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('link', null, array('label' => 'Ссылка на оригинал'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'));
    }

    /**
     * configureListFields
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('link', null, array('label' => 'Ссылка на оригинал'))
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
     * configureFormFields
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $images = $this->getSubject();
        if (!$this->hasParentFieldDescription()) { // this Admin is not embedded
            $formMapper->add('products', 'entity', [
                'class' => 'AppBundle\Entity\Products',
                'label' => 'Модель'
            ]);
        }
        $formMapper->add('link', null, array('label' => 'Ссылка на оригинал'));

        // You can then do things with the $images, like show a thumbnail in the help:
        $fileFieldOptions = ['required' => false];

        if ($images && ($webPath = $images->getLink())) {
            $fileFieldOptions['help'] = '<img src="' . $webPath . '" class="admin-preview" />';
        }

        $formMapper
            ->add('file', 'file', $fileFieldOptions)
            ->add('priority', null, array('label' => 'Приоритет', 'required' => false));
    }

    /**
     * configureShowFields
     *
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('link', null, array('label' => 'Ссылка на оригинал'))
            ->add('createTime', null, array('label' => 'Дата создания'))
            ->add('updateTime', null, array('label' => 'Дата последнего изменения'));
    }

    /**
     * getBatchActions
     *
     * retrieve the default batch actions (currently only delete)
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        if (
            $this->hasRoute('edit') && $this->isGranted('EDIT') &&
            $this->hasRoute('delete') && $this->isGranted('DELETE')
        ) {
            $actions['merge'] = array(
                'label' => 'action_merge',
                'translation_domain' => 'SonataAdminBundle',
                'ask_confirmation' => true,
            );

        }

        return $actions;
    }
}
