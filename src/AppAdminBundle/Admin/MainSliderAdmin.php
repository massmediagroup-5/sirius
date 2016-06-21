<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MainSliderAdmin extends Admin
{

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array('label' => 'Название'))
            ->addIdentifier('alias', null, array('label' => 'Ссылка'))
            ->add('priority', null, array('label' => 'Сортировка', 'editable' => true))
            ->add('active', null, array('label' => 'Активность(вкл/выкл)', 'editable' => true))
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
//        $fileFieldOptions = array('label'=>'Файл','required' => false);
//        $webPath = $image->getWebPath();
//        if ($image && ($webPath != '/img/slider/')) {
//            // get the container so the full path to the image can be set
//            $container = $this->getConfigurationPool()->getContainer();
//            $fullPath = $container->get('request')->getBasePath().$webPath;
//
//            // add a 'help' option containing the preview's img tag
//            $fileFieldOptions['help'] = '<img src="'.$fullPath.'" class="admin-preview" />';
//        }

        $formMapper
            ->add('title',null,array('label'=>'Название'))
            ->add('buttonText',null,array('label'=>'Текст кнопки'))
            ->add('alias',null,array('label'=>'Ссылка'))
            ->add('description',null,array('label'=>'Описание','attr' => array('class' => 'ckeditor')))
//            ->add('file', 'file',$fileFieldOptions)
            ->add('picture', 'comur_image', array(
                'uploadConfig' => array(
                    'uploadRoute' => 'comur_api_upload',        //optional
                    'uploadUrl' => $image->getUploadRootDir(),       // required - see explanation below (you can also put just a dir path)
                    'webDir' => $image->getUploadDir(),              // required - see explanation below (you can also put just a dir path)
                    'fileExt' => '*.jpg;*.gif;*.png;*.jpeg',    //optional
                    'libraryDir' => null,                       //optional
                    'libraryRoute' => 'comur_api_image_library', //optional
                    'showLibrary' => true,                      //optional
//                    'saveOriginal' => 'originalImage',          //optional
                    'generateFilename' => true          //optional
                ),
                'cropConfig' => array(
                    'minWidth' => 1920,
                    'minHeight' => 770,
                    'aspectRatio' => true,              //optional
                    'cropRoute' => 'comur_api_crop',    //optional
                    'forceResize' => false,             //optional
                    'thumbs' => array(                  //optional
                        array(
                            'maxWidth' => 192,
                            'maxHeight' => 77,
//                            'useAsFieldImage' => true  //optional
                        )
                    )
                )
            ))
            ->add('priority',null,array('label'=>'Сортировка'))
            ->add('active',null,array('label'=>'Активность(вкл/выкл)'))
        ;
    }

//    public function prePersist($image)
//    {
//        $this->manageFileUpload($image);
//    }
//
//    public function preUpdate($image)
//    {
//        $this->manageFileUpload($image);
//    }

//    private function manageFileUpload($image)
//    {
//        $image->setUpdateTime(new \DateTime());
//    }
}
