<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class SocialNetworksAdmin extends Admin
{
//    /**
//     * @param DatagridMapper $datagridMapper
//     */
//    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
//    {
//        $datagridMapper
//            ->add('id')
//            ->add('name')
//            ->add('link')
//            ->add('picture')
//            ->add('active')
//        ;
//    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('link')
//            ->add('picture')
            ->add('active')
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
        $formMapper
            ->add('name')
            ->add('link')
            ->add('picture', 'comur_image', array(
                'uploadConfig' => array(
                    'uploadRoute' => 'comur_api_upload',        //optional
                    'uploadUrl' => $image->getUploadRootDir(),       // required - see explanation below (you can also put just a dir path)
                    'webDir' => $image->getUploadDir(),              // required - see explanation below (you can also put just a dir path)
                    'fileExt' => '*.jpg;*.gif;*.png;*.jpeg',    //optional
                    'libraryDir' => null,                       //optional
                    'libraryRoute' => 'comur_api_image_library', //optional
                    'showLibrary' => true,                      //optional
                    'generateFilename' => true          //optional
                ),
                'cropConfig' => array(
                    'minWidth' => 45,
                    'minHeight' => 45,
                    'aspectRatio' => true,              //optional
                    'cropRoute' => 'comur_api_crop',    //optional
                    'forceResize' => false,             //optional
                    'thumbs' => array(                  //optional
                        array(
                            'maxWidth' => 45,
                            'maxHeight' => 45
                        )
                    )
                )
            ))
            ->add('active')
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
//            ->add('link')
//            ->add('picture')
//            ->add('active')
//        ;
//    }
}
