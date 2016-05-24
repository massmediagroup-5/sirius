<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProductColorsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
//            ->add('id')
            ->add('name', null, array('label' => 'Название цвета'))
            ->add('hex', null, array('label' => 'HEX код цвета'))
            ->add('createTime')
            ->add('updateTime')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
//            ->add('id')
            ->add('name', null, array('label' => 'Название цвета'))
            ->add('hex', null, array(
                'label' => 'Цвет и HEX код',
                'template' => 'AppAdminBundle:list:list.template.modelcolor.html.twig'
            ))
            ->add('createTime')
            ->add('updateTime')
            ->add('_action', 'actions', array(
                'actions' => array(
//                    'show' => array(),
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
        $color = $this->getSubject();
        $hexFieldOptions = ['label' => 'HEX код цвета'];

        if ($color && ($hex = $color->getHex())) {
            $hexFieldOptions['help'] = '<div style="width: 50px;height: 50px;background-color: '. $hex .'">&nbsp;</div>';
        }
        $formMapper
            ->add('name', null, array('label' => 'Название цвета'))
            ->add('hex', null, $hexFieldOptions)
        ;
    }

//    /**
//     * @param ShowMapper $showMapper
//     */
//    protected function configureShowFields(ShowMapper $showMapper)
//    {
//        $showMapper
//            ->add('id')
//            ->add('name')
//            ->add('hex')
//            ->add('createTime')
//            ->add('updateTime')
//        ;
//    }
}
