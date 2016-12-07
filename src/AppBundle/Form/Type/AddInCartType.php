<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $builder
            ->add('size', 'entity', [
                'class' => 'AppBundle:ProductModelSpecificSize',
                'choices' => $options['model']->getSizes(),
                'required' => true,
                'placeholder' => '',
                'constraints' => [new NotBlank],
                'choice_attr' => function ($val, $key, $index) {
                    return [
                        'data-preorderflag' => (int)$val->getCheckPreOrder(),
                        'data-id' => $val->getId(),
                    ];
                },
            ])
            ->add('quantity', TextType::class, [
                'required' => true,
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
