<?php

namespace AppAdminBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MenuItemsAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'menu.id'  // name of the ordered field
        // (default = the model's id field, if any)

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('link')
            ->add('linkType')
            ->add('parent.name')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('priority', 'string', array('editable' => true))
            ->add('menu.description')
            ->add('parent.name')
            ->add('link')
            ->add('linkType')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('menu','entity',array(
                'class'=> 'AppBundle:Menu',
                'property'=> 'description',
            ))
            ->add('parent', 'entity', ['label' => 'Родитель', 'class' =>
                'AppBundle:MenuItems',
                'required' => false,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('i')->where('i.menu = :menu')
                        ->setParameter('menu', $this->getSubject()->getMenu() ? $this->getSubject()->getMenu()->getId() : null) ;
                }
            ])
            ->add('name', 'text', array('label' => 'Menu item name'))
            ->add('priority', 'text', array('label' => 'Menu priority'))
            ->add('link', 'text', array('label' => 'Url'))
            ->add('linkType', 'text', array('label' => 'Url type'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('priority')
            ->add('link')
            ->add('linkType')
            ->add('createTime')
            ->add('updateTime')
        ;
    }
}
