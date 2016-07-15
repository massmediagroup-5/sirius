<?php

namespace AppAdminBundle\Form;

use AppAdminBundle\Admin\ImportAdmin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', [
                'label' => 'Тип',
                'choices' => [
                    (string)ImportAdmin::ACTUALIZE_TYPE => 'Актуализация',
                    (string)ImportAdmin::APPEND_TYPE => 'Приход',
                ]
            ])
            ->add('file', 'file', ['required' => true])
            ->add('submit', 'submit');
    }
}
