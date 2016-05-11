<?php

namespace AppAdminBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SonataShareFiltersType
 * @package AppAdminBundle\Form\Type
 */
class SonataShareFiltersType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sizes', 'entity', [
                'class' => 'AppBundle:ProductModelSizes',
                'label' => 'Размеры',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'required' => false,
                'data' => $options['group']->getSizes()
            ])
            ->add('colors', 'entity', [
                'class' => 'AppBundle:ProductColors',
                'label' => 'Цвета',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'data' => $options['group']->getColors()
            ])
            ->add('characteristicValues', 'entity', [
                'class' => 'AppBundle:CharacteristicValues',
                'label' => 'Характеристики',
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'data' => $options['group']->getCharacteristicValues(),
                'group_by' => function($value, $key, $index) {
                    return $value->getCharacteristics()->getName();
                }
            ])
            ->add('filter', SubmitType::class, [
                'label' => 'Отфильтровать товары'
            ])
            ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'group',
        ]);
    }
}
