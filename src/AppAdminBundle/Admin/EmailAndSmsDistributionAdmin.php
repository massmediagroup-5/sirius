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
            ->add('active', null, ['label'=>'Активность(вкл/выкл)'])
            ->add('createTime', 'doctrine_orm_datetime_not_strict', ['label'=>'Время создания'])
            ->add('updateTime', 'doctrine_orm_datetime_not_strict', ['label'=>'Время последнего обновления'])
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
            ->add('active', null, ['label'=>'Активность(вкл/выкл)', 'editable'=>true])
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
                    ->add('active', null, ['label'=>'Активность(вкл/выкл)'])
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
            ->tab('Информация по смс',['tab_template'=>'AppAdminBundle:admin:sms_info.html.twig'])
            ->end()
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'AppAdminBundle:admin:distribution_edit.html.twig';
                break;

            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(parent::getFormTheme(), ['AppAdminBundle:Form:sonata_type_models_list.html.twig']);
    }
}
