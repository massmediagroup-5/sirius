<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeProductSizeQuantityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $quantities = range(1, 10);

        $builder
            ->add('quantity', ChoiceType::class, [
                'choices' => array_combine($quantities, $quantities),
                'required' => true,
                'placeholder' => '',
                'data' => isset($options['selected']) ? $options['selected'] : 1,
                'constraints' => [new NotBlank]
            ])->add('size', HiddenType::class, [
                'data' => $options['size'],
                'constraints' => [new NotBlank]
            ]);


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'size',
        ));
        $resolver->setOptional(array(
            'selected',
        ));
    }
}
