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
            ->add('name', null, array('label' => 'Название цвета'))
            ->add('hex', null, array('label' => 'HEX код цвета'))
            ->add('createTime', 'doctrine_orm_datetime_not_strict', ['label' => 'Дата создания'])
            ->add('updateTime', 'doctrine_orm_datetime_not_strict', ['label' => 'Дата последнего изменения'])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, array('label' => 'Название цвета'))
            ->add('hex', null, array(
                'label' => 'Цвет и HEX код',
                'template' => 'AppAdminBundle:list:list.template.modelcolor.html.twig'
            ))
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
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
