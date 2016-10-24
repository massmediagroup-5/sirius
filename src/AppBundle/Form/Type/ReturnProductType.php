<?php
namespace AppBundle\Form\Type;

use AppBundle\Validator\ReturnOrderNumConstraint;
use AppBundle\Validator\ReturnUserPhoneConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ProductReturnType
 * @package AppBundle\Form\Type
 */
class ReturnProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_phone', null, array('attr' => array(
                'class' => 'form-control',
                'placeholder' => 'введите email или телефон',
                'value' => $options['user'] ? $options['user']->getPhone() : '',
            ),
                //'mapped' => false,
                'constraints' => array(new ReturnUserPhoneConstraint())
            ))
            ->add('order_id', null, array('attr' => array(
                'class' => 'form-control',
                'placeholder' => 'введите номер заказа',
            ),
                //'mapped' => false,
                'constraints' => array(new ReturnOrderNumConstraint())
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'user',
        ));
    }
}
