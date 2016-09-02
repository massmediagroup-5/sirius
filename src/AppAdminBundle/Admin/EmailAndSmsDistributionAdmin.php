<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EmailAndSmsDistributionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label'=>'Название рассылки'])
            ->add('sendSms', null, ['label'=>'Отправка смс'])
            ->add('smsText', null, ['label'=>'Текст смс'])
            ->add('sendEmail', null, ['label'=>'Отправлять Email'])
            ->add('emailTitle', null, ['label'=>'Название Email'])
            ->add('emailText', null, ['label'=>'Текст Email'])
            ->add('createTime', null, ['label'=>'Время создания'])
            ->add('updateTime', null, ['label'=>'Время последнего обновления'])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, ['label'=>'Название рассылки'])
            ->add('sendSms', null, ['label'=>'Отправка смс', 'editable'=>true])
            ->add('sendEmail', null, ['label'=>'Отправлять Email', 'editable'=>true])
            ->add('emailTitle', null, ['label'=>'Название Email'])
            ->add('createTime', null, ['label'=>'Время создания'])
            ->add('updateTime', null, ['label'=>'Время последнего обновления'])
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
            ->tab('Общая информация о рассылке')
                ->with('Общая информация о рассылке', ['class' => 'col-md-12',])
                    ->add('name', null, ['label'=>'Название рассылки'])
                    ->add('sendSms', null, ['label'=>'Отправка смс'])
                    ->add('smsText', null, ['label'=>'Текст смс'])
                    ->add('sendEmail', null, ['label'=>'Отправлять Email'])
                    ->add('emailTitle', null, ['label'=>'Название Email'])
                    ->add('emailText', null, ['label'=>'Текст Email','attr' => ['class' => 'ckeditor']])
                ->end()
            ->end()
            ->tab('Пользователи')
                ->with('Пользователи', ['class' => 'col-md-12',])
                    ->add('users', 'sonata_type_models_list',
                        [
                            'label' => 'Пользователи',
                            'class' => 'AppBundle:Users',
                            'model_manager' => $this->getModelManager()
                        ],
                        [
                            'admin_code' => 'sonata.user.admin.user'
                        ]
                    )
                ->end()
            ->end()
            ->tab('Информация по смс')
                ->with('Информация по смс', ['class' => 'col-md-12',])
                        ->add('smsInfos', null, ['label'=>'Информация о смс'])
                ->end()
            ->end()
        ;
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(parent::getFormTheme(), ['AppAdminBundle:Form:sonata_type_models_list.html.twig']);
    }
}
