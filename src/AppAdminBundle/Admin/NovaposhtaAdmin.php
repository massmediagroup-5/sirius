<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class NovaposhtaAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('apiKey', null, ['label' => 'Ключ API', 'editable'=>true])
            ->add('active', null, ['label' => 'Активность(вкл/выкл)', 'editable'=>true])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('apiKey', null, ['label' => 'Ключ API', 'editable'=>true])
            ->add('active', null, ['label' => 'Активность(вкл/выкл)', 'editable'=>true])
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
            ->add('apiKey', null, ['label' => 'Ключ API'])
            ->add('active', null, ['label' => 'Активность(вкл/выкл)'])
        ;
    }

    public function getExportFormats()
    {
        return array();
    }

}
