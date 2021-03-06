<?php
namespace AppBundle\Form\Type;

use AppBundle\Validator\ReturnOrderNumConstraint;
use AppBundle\Validator\ReturnUserPhoneConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'class' => 'form-control phone-inp',
                'placeholder' => 'введите телефон',
                'required' => true
            ),
                'data' => $options['user'] ? $options['user']->getPhone() : null,
                'constraints' => array(new ReturnUserPhoneConstraint())
            ))
            ->add('order_id', null, array('attr' => array(
                'class' => 'form-control',
                'placeholder' => 'введите номер заказа',
                'required' => true,

            ),
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
