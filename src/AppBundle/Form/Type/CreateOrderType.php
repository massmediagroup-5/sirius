<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\Orders;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateOrderType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = isset($options['request']) ? $options['request'] : false;
        $delivery = $request ? $request->get('create_order')['delivery_type'] : false;
        $city = false;
        if($delivery) {
            if($delivery == 'np') {
                $city = $request->get('create_order')['np_delivery_city'];
            } else {
                $city = $request->get('create_order')['del_delivery_city'];
            }
        }
        $user = Arr::get($options, 'user');

        $paymentTypes = [
            Orders::PAY_TYPE_BANK_CARD,
            Orders::PAY_TYPE_COD,
        ];

        $builder
            ->add('phone', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank],
                'data' => $user ? $user->getPhone() : ''
            ])
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank],
                'data' => $user ? $user->getName() : ''
            ])
            ->add('surname', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank],
                'data' => $user ? $user->getSurname() : ''
            ])
            ->add('email', TextType::class, [
                'constraints' => [new Email],
                'data' => $user ? $user->getEmail() : ''
            ])
            ->add('comment', TextareaType::class)
            ->add('np_delivery_city', 'entity', [
                'class' => 'AppBundle:Cities',
                'required' => true,
                'constraints' => $delivery == 'np' ? [new NotBlank] : [],
                'placeholder' => ''
            ])
            ->add('np_delivery_store', 'entity', [
                'class' => 'AppBundle:Stores',
                'required' => true,
                'constraints' => $delivery == 'np' ? [new NotBlank] : [],
                'query_builder'  => function (EntityRepository $er) use($city) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC')->where('s.cities = :city')
                        ->setParameter('city', $city);
                },
                'placeholder' => ''
            ])
            ->add('delivery_type', ChoiceType::class, [
                'choices' => [
                    'np' => 'np',
                    'del' => 'del',
                ],
                'required' => true,
                'data' => 'np',
                'expanded' => true,
            ])
            ->add('pay', ChoiceType::class, [
                'choices' => array_combine($paymentTypes, $paymentTypes),
                'required' => true,
                'data' => Orders::PAY_TYPE_BANK_CARD,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class);

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional(array(
            'request',
            'user',
        ));
    }

}
