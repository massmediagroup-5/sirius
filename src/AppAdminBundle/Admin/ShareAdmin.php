<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\ShareSizesGroup;
use AppBundle\Event\ShareGroupFiltersUpdated;
use Illuminate\Support\Arr;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class ShareAdmin
 * @package AppAdminBundle\Admin
 */
class ShareAdmin extends Admin
{
    protected $datagridValues = [
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'createTime'  // name of the ordered field
    ];

    protected $sizesPerPage = 15;

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('add_sizes_group', $this->getRouterIdParameter() . '/add_sizes_group', [], [], [
                'expose' => true
            ])
            ->add('remove_sizes_group', $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/remove', [], [],
                [
                    'expose' => true
                ])
            ->add('update_sizes_group',
                $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/update_sizes_group', [], [],
                [
                    'expose' => true
                ])
            ->add('get_sizes', $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/get_sizes', [], [], [
                'expose' => true
            ])
            ->add('get_conflict_sizes',
                $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/get_conflict_sizes',
                [], [], ['expose' => true])
            ->add('get_filters_sizes',
                $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/get_filters_sizes', [], [], [
                    'expose' => true
                ])
            ->add('toggle_group_model',
                $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/toggle_group_model/{model_id}', [], [], [
                    'expose' => true
                ])
            ->add('toggle_group_size',
                $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/toggle_group_size/{size_id}', [], [], [
                    'expose' => true
                ])
            ->add('toggle_group_except_model',
                $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/toggle_group_except_model/{model_id}',
                [], [], ['expose' => true])
            ->add('toggle_group_except_size',
                $this->getRouterIdParameter() . '/sizes_group/{sizes_group_id}/toggle_group_except_size/{size_id}', [],
                [], ['expose' => true]);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, ['label' => 'Имя'])
            ->add('createTime', null, ['label' => 'Время оформления'])
            ->add('startTime', null, ['label' => 'Время начала'])
            ->add('endTime', null, ['label' => 'Время окончания'])
            ->add('priority', null, ['label' => 'Приоритет']);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        $listMapper
            ->addIdentifier('id', null, ['label' => 'ID'])
            ->add('name', null, ['label' => 'Имя', 'route' => ['name' => 'edit']])
            ->add('createTime', null, ['label' => 'Время создания'])
            ->add('startTime', null, ['label' => 'Время начала'])
            ->add('endTime', null, ['label' => 'Время окончания'])
            ->add('status', null, ['editable' => true])
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $classNames = $this->getConfigurationPool()->getContainer()->get('share')->getNamedClassNames();
        $image = $this->getSubject()->getImage();

        $formMapper
            ->tab('Акция')
            ->with('Акция', ['class' => 'col-md-12'])
            ->add('name', null, ['label' => 'Имя'])
            ->add('description', null, ['label' => 'Описание', 'attr' => ['class' => 'ckeditor']])
            ->add('startTime', null, ['label' => 'Время начала'])
            ->add('endTime', null, ['label' => 'Время окончания'])
            ->add('image', 'file', [
                'label' => 'Картинка',
                'data_class' => null,
                'help' => $image ? "<img src='{$image}' class='admin-preview' />" : false
            ])
            ->add('status', null, ['label' => 'Статус', 'required' => false])
            ->add('className', 'choice', [
                'label' => 'Имя класса',
                'choices' => $classNames,
                'required' => false
            ])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->end()
            ->end();

        if ($this->subject->getId()) {
            $formMapper->tab('Список групп', [
                'tab_template' => 'AppAdminBundle:admin:share_groups.html.twig'
            ])
                ->end();
        }
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'AppAdminBundle:admin:share_edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    /**
     * @param ShareSizesGroup $group
     * @param $filters
     * @return mixed
     */
    public function paginateModels(ShareSizesGroup $group, $filters = [])
    {
        $container = $this->getConfigurationPool()->getContainer();
        $models = $container->get('doctrine')
            ->getRepository("AppBundle:ProductModels")
            ->getAdminSharesQuery($group);

        $models = $container->get('knp_paginator')->paginate(
            $models,
            Arr::get($filters, 'page', 1),
            $this->sizesPerPage,
            ['wrap-queries' => true]
        );

        return $models;
    }

    /**
     * @param $filters
     * @return mixed
     */
    public function paginateModelsToSelect($filters = [])
    {
        $container = $this->getConfigurationPool()->getContainer();
        $models = $container->get('doctrine')
            ->getRepository("AppBundle:ProductModels")
            ->getAdminSearchQuery($filters);

        $models = $container->get('knp_paginator')->paginate(
            $models,
            Arr::get($filters, 'page', 1),
            $this->sizesPerPage,
            ['wrap-queries' => true]
        );

        return $models;
    }

    /**
     * @param ShareSizesGroup $sizesGroup
     * @param $data
     * @param $save
     * @return mixed
     */
    public function updateGroup(ShareSizesGroup $sizesGroup, $data, $save = true)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $em = $container->get('doctrine')
            ->getManager();

        $sizesGroup->setSizes(Arr::get($data, 'sizes', []));
        $sizesGroup->setColors(Arr::get($data, 'colors', []));
        $sizesGroup->setCharacteristicValues(Arr::get($data, 'characteristicValues', []));

        if ($save) {
            $em->persist($sizesGroup);
            $em->flush();
            $container->get('event_dispatcher')->dispatch('app.share_group_filters_updated',
                new ShareGroupFiltersUpdated($sizesGroup));
        }
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->tab('Акция')
            ->with('Акция', ['class' => 'col-md-12'])
            ->add('name', null, ['label' => 'Имя'])
            ->add('description', null, ['label' => 'Описание'])
            ->add('priority', null, ['label' => 'Приоритет'])
            ->add('createTime', null, ['label' => 'Дата создания'])
            ->end()
            ->end();
    }

    /**
     * @param array $newFilters
     * @return array
     */
    public function paramsToGetSizes($newFilters)
    {
        $parameters = array_merge($this->request->request->all(), $newFilters);

        return json_encode($parameters);
    }

    public function prePersist($object)
    {
        $this->uploadImage($object);
    }

    public function preUpdate($object)
    {
        $this->uploadImage($object);
    }

    /**
     * @param $object
     */
    protected function uploadImage($object)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $imagePath = $container->get('uploader')->upload($container->getParameter('upload_shares_img_directory'),
            $object->getImage());

        $object->setImage($imagePath);
    }

}
