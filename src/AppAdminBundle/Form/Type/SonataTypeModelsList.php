<?php

namespace AppAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\LegacyChoiceListAdapter;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\AdminBundle\Form\DataTransformer\ModelsToArrayTransformer;
use Sonata\AdminBundle\Form\EventListener\MergeCollectionListener;
use Sonata\AdminBundle\Form\ChoiceList\ModelChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Class StoresAutoCompleteType
 * @package AppAdminBundle\Form\Type
 */
class SonataTypeModelsList extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new MergeCollectionListener($options['model_manager']))
            ->addViewTransformer(new ModelsToArrayTransformer($options['choice_list']), true);

    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($view->vars['sonata_admin'])) {
            // set the correct edit mode
            $view->vars['sonata_admin']['edit'] = 'list';
        }
        $view->vars['btn_add'] = $options['btn_add'];
        $view->vars['btn_list'] = $options['btn_list'];
        $view->vars['btn_delete'] = $options['btn_delete'];
        $view->vars['btn_catalogue'] = $options['btn_catalogue'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'compound'          => false,
            'template'          => 'choice',
            'model_manager'     => null,
            'class'             => null,
            'query'             => null,
            'property'          => null,
            'choices'           => null,
            'multiple'          => true,
            'expanded'          => false,
            'preferred_choices' => array(),
            'btn_add'           => 'link_add',
            'btn_list'          => 'link_list',
            'btn_delete'        => 'link_delete',
            'btn_catalogue'     => 'SonataAdminBundle',
            'choice_list'       => function (Options $options, $previousValue) {
                return new LegacyChoiceListAdapter(new ModelChoiceList($options['model_manager'], $options['class'],
                    $options['property'], $options['query'], $options['choices']));
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_type_models_list';
    }
}