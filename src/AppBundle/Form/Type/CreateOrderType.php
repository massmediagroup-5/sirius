<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateOrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('size', 'entity', [
                'class' => 'AppBundle:ProductModelSizes',
                'choices' => $options['model']->getSizes(),
                'required' => true,
                'placeholder' => ''
            ])
            ->add('quantity', ChoiceType::class, [
                'choices' => range(1, 10),
                'required' => true,
                'placeholder' => ''
            ])
            ->add('submit', SubmitType::class);


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'model',
        ));
    }
}
