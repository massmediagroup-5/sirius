<?php

namespace AppAdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('vendor', 'entity', [
                'class' => 'AppBundle:Vendors',
                'required' => true,
                'choice_label' => function ($vendor, $key, $index) {
                    return strtoupper($vendor->getName());
                },
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder()
//                }

            ])
            ->add('file', 'file', ['required' => true])
            ->add('submit', 'submit');
    }
}
