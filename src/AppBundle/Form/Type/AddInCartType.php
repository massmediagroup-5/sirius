<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddInCartType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $quantities = range(1, 10);
        $builder
            ->add('size', 'entity', [
                'class' => 'AppBundle:ProductModelSizes',
                'choices' => $options['model']->getSizes(),
                'required' => true,
                'placeholder' => '',
                'constraints' => [new NotBlank]
            ])
            ->add('quantity', ChoiceType::class, [
                'choices' => array_combine($quantities, $quantities),
                'required' => true,
                'placeholder' => '',
                'constraints' => [new NotBlank]
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
