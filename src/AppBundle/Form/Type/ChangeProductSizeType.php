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
                'choices' => $options['size']->getModel()->getSizes(),
                'required' => true,
                'data' => $options['size'],
                'constraints' => [new NotBlank]
            ])->add('old_size', HiddenType::class, [
                'data' => $options['size']->getId(),
                'constraints' => [new NotBlank]
            ]);


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'size',
        ]);
    }
}
