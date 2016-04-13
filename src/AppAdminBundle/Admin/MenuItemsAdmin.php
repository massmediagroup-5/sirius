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
    );

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Название пункта меню'))
            ->add('link', null, array('label' => 'Ссылка'))
            ->add('linkType', 'doctrine_orm_choice', array('label' => 'Тип ссылки'),
                'choice',
                array(
                    'choices' => array(
                        'local' => 'Локальная',  // The key (value1) will contain the actual value that you want to filter on
                        'global' => 'Внешняя',  // The 'Name Two' is the "display" name in the filter
                    ),
                    'expanded' => true,
                    'multiple' => true))
            ->add('parent.name', null, array('label' => 'Название меню'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'Название пункта меню'))
            ->add('priority', null, array('editable' => true, 'label'=>'Приоритет'))
            ->add('menu.description', null, array('label' => 'Название меню'))
            ->add('parent.name', null, array('label' => 'Название родительского пункта меню'))
            ->add('link', null, array('label' => 'Ссылка'))
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
                'label'=>'Меню'
            ))
            ->add('parent', 'entity', ['label' => 'Родитель', 'class' =>
                'AppBundle:MenuItems',
                'required' => false,
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('i')->where('i.menu = :menu')
                        ->setParameter('menu', $this->getSubject()->getMenu() ? $this->getSubject()->getMenu()->getId() : null) ;
                }
            ])
            ->add('name', 'text', array('label' => 'Название пункта меню'))
            ->add('priority', 'text', array('label' => 'Приоритет вывода'))
            ->add('link', 'text', array('label' => 'Ссылка'))
            ->add('linkType', 'choice', array(
                'label' => 'Тип ссылки',
                'choices' => array(
                    'local' => 'Локальная',  // The key (value1) will contain the actual value that you want to filter on
                    'global' => 'Внешняя',  // The 'Name Two' is the "display" name in the filter
                )
            ))
        ;
    }
}
