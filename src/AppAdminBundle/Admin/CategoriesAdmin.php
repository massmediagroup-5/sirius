<?php

namespace AppAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategoriesAdmin extends Admin
{

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, ['label' => 'Название категории'])
            ->add('alias', null, ['label' => 'Ссылка'])
            ->add('parrent.name', null, ['label' => 'Родительская категория'])
            ->add('inMenu', null, ['label' => 'В меню'])
            ->add('active', null, ['label' => 'Актиная'])
            ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
            ->add('seoDescription', null, ['label' => 'СЕО описание'])
            ->add('seoKeywords', null, ['label' => 'СЕО кейворды'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->addIdentifier('name', null, ['label' => 'Название категории'])
            ->addIdentifier('alias', null, ['label' => 'Ссылка'])
            ->add('parrent.name', 'entity',
                [
                    'class'         => 'AppBundle:Categories',
                    'associated_property'      => 'name',
                    'label'         => 'Родительская категория',
                    'editable' => true
                ]
            )
            ->add('inMenu', 'boolean', ['label' => 'В меню', 'editable' => true])
            ->add('active', 'boolean', ['label' => 'Актиная', 'editable' => true])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'AppAdminBundle:admin:category_edit.html.twig';
                break;

            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $id = $this->id($this->getSubject());

        $formMapper
            ->tab('Категория')
                ->with('Categories',
                    [
                        'class'       => 'col-md-12',
                    ])
                    ->add('name', null, ['label' => 'Название категории'])
                    ->add('alias', null, ['label' => 'Ссылка'])
                    ->add('parrent','entity',
                        [
                            'class'         => 'AppBundle:Categories',
                            'property'      => 'name',
                            'label'         => 'Родительская категория',
                            'empty_value'   => 'Выберите родительскую категорию'
                        ]
                    )
                    ->add('inMenu', null, ['label' => 'В меню'])
                    ->add('active', null, ['label' => 'Актиная'])
                    ->add('content', null, ['label' => 'Контент'])
                    ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
                    ->add('seoDescription', null, ['label' => 'СЕО описание'])
                    ->add('seoKeywords', null, ['label' => 'СЕО кейворды'])
                ->end()
            ->end()
            ->tab('Характеристик')
                ->with('Characteristics',
                    [
                        'class'       => 'col-md-12',
                        'collapsed' => true
                    ])
                    ->add('characteristics', 'entity',
                        [
                            'class'     => 'AppBundle:Characteristics',
                            'label'     => 'Характеристик',
                            'expanded' => true,
                            'multiple' => true,
                            'by_reference' => false,
                        ]
                    )
                ->end()
            ->end()
            ->tab('Значения характеристик')
                ->with('CharacteristicValues',
                    [
                        'class'       => 'col-md-12',
                        'collapsed' => true
                    ])
                    ->add('characteristicValues', 'entity',
                        [
                            'class'    => 'AppBundle:CharacteristicValues' ,
                            'label' => 'Значения характеристик',
                            'expanded' => true,
                            'multiple' => true,
                            'by_reference' => false,
                            'query_builder' => 
                                function($er) use ($id){
                                    $qb = $er->createQueryBuilder('characteristicValues');
                                    $qb->select('characteristicValues')
                                        ->innerJoin('characteristicValues.characteristics', 'characteristics')->addSelect('characteristics')
                                        ->innerJoin('characteristics.categories', 'categories')->addSelect('categories')
                                        ->where('categories.id = :id')->setParameter('id', $id)
                                    ;
                                    return $qb;
                                }
                        ]
                    )
                ->end()
            ->end()
            ->tab('Фильтры')
                ->with('Filters',
                    [
                        'class'       => 'col-md-12',
                        'collapsed' => true
                    ])
                    ->add('filters', 'sonata_type_model',
                        [
                            'class'     => 'AppBundle:Filters',
                            'label'     => 'Фильтры',
                            'property'=> 'name',
                            'by_reference' => false,
                        ]
                    )
                ->end()
            ->end()
        ;
    }

    public function postUpdate($object)
    {
        $this->getConfigurationPool()->getContainer()->get('checkRelationship')
            ->checkVsCharacteristicValues($object->getId());
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name', null, ['label' => 'Название категории'])
            ->add('alias', null, ['label' => 'Ссылка'])
            ->add('parrent', 'entity', ['label' => 'Родительская категория'])
            ->add('inMenu', null, ['label' => 'В меню'])
            ->add('active', null, ['label' => 'Актиная'])
            ->add('seoTitle', null, ['label' => 'СЕО заглавие'])
            ->add('seoDescription', null, ['label' => 'СЕО описание'])
            ->add('seoKeywords', null, ['label' => 'СЕО кейворды'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->add('updateTime', null, ['label' => 'Дата последнего изменения'])
        ;
    }
}
