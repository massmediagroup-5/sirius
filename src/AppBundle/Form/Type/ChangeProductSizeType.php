<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeProductSizeType extends AbstractType
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
                'data' => isset($options['selectedSize']) ? $options['selectedSize'] : null,
                'constraints' => [new NotBlank]
            ])->add('old_size', HiddenType::class, [
                'data' => isset($options['selectedSize']) ? $options['selectedSize']->getId() : null,
                'constraints' => [new NotBlank]
            ]);


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'model',
        ));

        $resolver->setOptional(array(
            'selectedSize',
        ));
    }
}
