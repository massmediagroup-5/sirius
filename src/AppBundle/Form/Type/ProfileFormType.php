<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileFormType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone', TextType::class)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->remove('username')
            ->remove('current_password')
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false
            ])
            ->add('submit', SubmitType::class);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_profile';
    }

    public function getName()
    {
        return 'app_user_profile';
    }

}
