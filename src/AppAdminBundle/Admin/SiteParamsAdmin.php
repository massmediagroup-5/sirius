<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SiteParamsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('paramName', null, array('label' => 'Название параметра'))
            ->add('paramValue', null, array('label' => 'Значение параметра'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('paramName', null, array('label' => 'Название параметра'))
            ->add('paramValue', 'text', array(
                'label' => 'Значение параметра',
                'template' => 'AppAdminBundle:list:param_value.html.twig',
            ))
            ->add('active', null, array('editable' => true, 'label' => 'Активность(вкл/выкл)'))
            ->add('_action', 'actions', array(
                'actions' => array(
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
        $param = $this->getSubject();
        if ($param->getEditor()) {
            $formMapper
                ->add('paramName', null, array('label' => 'Название параметра'))
                ->add('paramValue', 'textarea',
                    array('label' => 'Значение параметра', 'attr' => array('class' => 'ckeditor')))
                ->add('active', null, array('label' => 'Активность(вкл/выкл)'))
                ->add('editor', null, array('label' => 'Редактор(вкл/выкл)'));
        } else {
            $formMapper
                ->add('paramName', null, array('label' => 'Название параметра'));
            if ($param->getParamName() == 'blockAllSite') {
                $formMapper->add('paramValue', 'choice', [
                    'label' => 'Значение параметра',
                    'choices' => [
                        0 => 'Сайт включен',
                        1 => 'Сайт выключен',
                    ]
                ]);
            } else {
                $formMapper->add('paramValue', 'text', array('label' => 'Значение параметра'));
            }

            $formMapper->add('active', null, array('label' => 'Активность(вкл/выкл)'))
                ->add('editor', null, array('label' => 'Редактор(вкл/выкл)'));
        }
    }
}
