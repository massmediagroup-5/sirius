<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MainBannersAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('alias')
            ->add('picture')
            ->add('priority')
            ->add('active')
            ->add('createTime', null, array('label' => 'Время создания'))
            ->add('updateTime', null, array('label' => 'Время последнего обновления'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array('label' => 'Название'))
            ->addIdentifier('alias', null, array('label' => 'Ссылка'))
            ->add('wide', null, array('label' => 'Широкий(да/нет)', 'editable' => true))
            ->add('active', null, array('label' => 'Активность(вкл/выкл)', 'editable' => true))
            ->add('priority', null, array('label' => 'Приоритет', 'editable' => true))
            ->add('createTime', null, array('label' => 'Время создания'))
            ->add('updateTime', null, array('label' => 'Время последнего обновления'))
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
        $image = $this->getSubject();

        // use $fileFieldOptions so we can add other options to the field
        $fileFieldOptions = array('label'=>'Файл','required' => false);
        $webPath = $image->getWebPath();
        if ($image && ($webPath != '/img/banners/')) {
            // get the container so the full path to the image can be set
            $container = $this->getConfigurationPool()->getContainer();
            $fullPath = $container->get('request')->getBasePath().$webPath;

            // add a 'help' option containing the preview's img tag
            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
        }

        $formMapper
            ->add('title',null,array('label'=>'Название'))
            ->add('titleButton',null,array('label'=>'Название1'))
            ->add('alias',null,array('label'=>'Ссылка'))
            ->add('file', 'file',$fileFieldOptions)
            ->add('priority',null,array('label'=>'Приоритет','attr'=>['style'=>'width:120px;']))
            ->add('wide',null,array('label'=>'Широкий(да/нет)'))
            ->add('active',null,array('label'=>'Активность(вкл/выкл)'))
        ;
    }

    public function prePersist($image)
    {
        $this->manageFileUpload($image);
    }

    public function preUpdate($image)
    {
        $this->manageFileUpload($image);
    }

    private function manageFileUpload($image)
    {
        $image->setUpdateTime(new \DateTime());
    }
}
