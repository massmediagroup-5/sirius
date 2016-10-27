<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\Carriers;
use AppBundle\Entity\Orders;
use AppBundle\Services\PricesCalculator;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CreateOrderType extends AbstractType
{
    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * @var PricesCalculator
     */
    protected $pricesCalculator;
    
    /**
     * CreateOrderType constructor.
     * @param PricesCalculator $pricesCalculator
     * @param ContainerInterface $container
     */
    public function __construct(PricesCalculator $pricesCalculator, ContainerInterface $container)
    {
        $this->pricesCalculator = $pricesCalculator;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = isset($options['request']) ? $options['request'] : false;
        $delivery = false;
        if (isset($request->get('create_order')['delivery_type'])){
            $delivery = $request ? $request->get('create_order')['delivery_type'] : false;
        }
        $city = false;
        if ($delivery) {
            if ($delivery == Carriers::NP_ID) {
                $city = $request->get('create_order')['np_delivery_city'];
            }
        }
        $user = Arr::get($options, 'user');
        $authenticated = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_ANONYMOUSLY');

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
                'constraints' => $delivery == Carriers::NP_ID ? [new NotBlank] : [],
                'placeholder' => ''
            ])
            ->add('np_delivery_store', 'entity', [
                'class' => 'AppBundle:Stores',
                'required' => true,
                'constraints' => $delivery == Carriers::NP_ID ? [new NotBlank] : [],
                'query_builder' => function (EntityRepository $er) use ($city) {
                    return $er->createQueryBuilder('s')->orderBy('s.name', 'ASC')->where('s.cities = :city')
                        ->setParameter('city', $city);
                },
                'placeholder' => ''
            ])
            ->add('delivery_type', EntityType::class, array(
                'class' => 'AppBundle:Carriers',
                'required' => true,
                'constraints' => [new NotBlank],
                'query_builder' => function (EntityRepository $er) use ($city) {
                    return $er->createQueryBuilder('c')->where('c.active = :active')
                              ->setParameter('active', 1);
                },
                'choice_label' => 'name',
                'expanded' => true,
            ))
            ->add('customDelivery', TextType::class, [
                'label'=>'Адресс доставки',
            ])
            ->add('pay', ChoiceType::class, [
                'choices' => array_combine($paymentTypes, $paymentTypes),
                'required' => true,
                'data' => Orders::PAY_TYPE_BANK_CARD,
                'expanded' => true,
            ])
            ->add('bonuses', HiddenType::class, [
                'constraints' => [
                    new GreaterThanOrEqual(0),
                    new LessThanOrEqual($authenticated ? $this->pricesCalculator->getMaxAllowedBonuses() : 0)
                ],
                'data' => 0
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
