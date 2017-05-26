<?php

namespace AppAdminBundle\Admin;

use AppBundle\Entity\Carriers;
use AppBundle\Entity\NovaposhtaSender;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NovaposhtaSenderAdmin extends Admin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, ['label' => '#'])
            ->add('name')
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('city')
            ->add('warehouse', null, ['label' => 'Активность(вкл/выкл)', 'editable'=>true])
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
            ->with("'Нова Пошта' отправитель", ['class' => 'col-md-6'])
            ->add('name', TextType::class, [])
            ->add('lastName', TextType::class, [])
            ->add('firstName', TextType::class, [])
            ->add('middleName', TextType::class, [])
            ->add('email', TextType::class, [])
            ->add('phone', TextType::class, [])
            ->add('city', 'entity', [
                'class' => 'AppBundle:Cities',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.active = 1 AND c.carriers = :carrier')
                        ->orderBy('c.name', 'ASC')
                        ->setParameter('carrier', Carriers::NP_ID);
                },
                'placeholder' => '',
            ])
            ->add('warehouse', 'sonata_stores_list', [
                'class' => 'AppBundle:Stores',
                'label' => 'Склад',
            ])
        ->end()
        ;
    }

    /**
     * @return array
     */
    public function getFormTheme()
    {
        return array_merge(parent::getFormTheme(), ['AppAdminBundle:Form:sonata_stores_list_edit.html.twig']);
    }

    /**
     * @param NovaposhtaSender $object
     * @return void
     */
    public function prePersist($object)
    {
        $novaPoshta = $this->getConfigurationPool()->getContainer()->get('novaposhta');
        $result = $novaPoshta->createContactPerson($object);
        $ref = $result->data[0]->Ref;
        $object->setRef($ref);
    }

    /**
     * @param NovaposhtaSender $object
     * @return void
     */
    public function preRemove($object)
    {
        $novaPoshta = $this->getConfigurationPool()->getContainer()->get('novaposhta');
        $novaPoshta->removeContactPerson($object);
    }
}
