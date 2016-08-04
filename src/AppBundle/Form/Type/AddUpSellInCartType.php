<?php
namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AddUpSellInCartType
 * @package AppBundle\Form\Type
 */
class AddUpSellInCartType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sizes', CollectionType::class, [
            'type' => HiddenType::class,
            'data' => array_map(function ($size) {
                return $size->getId();
            }, $options['sizes']),
            'allow_add' => true,
            'label' => false
        ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'sizes' => [],
            'validation_groups' => false,
        ]);
        $resolver->setOptional([
            'sizes',
        ]);
    }
}
