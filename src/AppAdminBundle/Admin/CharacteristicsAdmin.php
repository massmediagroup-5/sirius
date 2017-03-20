<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CharacteristicsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', 'doctrine_orm_callback',
                [
                    'label'       => 'Название',
                    'show_filter' => true,
                    'callback'    => function ($queryBuilder, $alias, $field, $value) {
                        if ( ! $value['value']) {
                            return false;
                        }
                        $queryBuilder
                            ->andWhere($alias . '.name LIKE :value')
                            ->setParameter('value', '%' . $value['value']->getName() . '%');

                        return true;
                    },
                ],
                'entity',
                [
                    'class'         => 'AppBundle:Characteristics',
                    'property'      => 'name',
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('o');
                            $qb->select('o')->groupBy('o.name');

                            return $qb;
                        }
                ]
            )
            ->add('inFilter', 'doctrine_orm_choice', ['label' => 'В фильтре'],
                'choice',
                [
                    'choices' => [
                        '1'                           => 'Да',
                        '0'                           => 'Нет',
                    ]
                ]
            )
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
            ->add('name', null, ['label' => 'Название'])
            ->add('inFilter', null, ['label' => 'В фильтре','editable' => true])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]
            )
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', null, ['label' => 'Название'])
            ->add('characteristicValues', 'sonata_type_collection', [
                'required' => false,
                'cascade_validation' => true,
                'by_reference' => false
            ], [
                'edit' => 'inline',
                'inline' => 'table'
            ])
            ->add('inFilter', null, ['label' => 'В фильтре'])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, ['label' => 'Название'])
            ->add('inFilter', null, ['label' => 'В фильтре'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
        ;
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(parent::getFormTheme(), ['AppAdminBundle:Form:sonata_type_models_list.html.twig']);
    }

    public function prePersist($object)
    {
        foreach ($object->getCharacteristicValues() as $cv) {
            $cv->setCharacteristics($object);
        }
    }

    public function preUpdate($object)
    {
        foreach ($object->getCharacteristicValues() as $cv) {
            $cv->setCharacteristics($object);
        }
    }
}
