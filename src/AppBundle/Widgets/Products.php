<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ShareSizesGroup;
use AppBundle\Form\Type\AddUpSellInCartType;
use AppBundle\Form\Type\ChangeProductSizeQuantityType;
use AppBundle\Form\Type\ChangeProductSizeType;
use AppBundle\Form\Type\RemoveProductSizeType;
use AppBundle\Model\CartSize;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Form\Type\AddInCartType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Class: Products
 * @author Zimm
 */
class Products
{
    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * templating
     *
     * @var mixed
     */
    private $templating;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->templating = $container->get('templating');
    }

    /**
     * Render wish button
     *
     * @param ProductModels $item
     * @param array $params
     * @return mixed
     */
    public function wishButton(ProductModels $item, $params = [])
    {
        $addedFlag = in_array($item->getId(), $this->container->get('wishlist')->getIds());

        return $this->templating->render('AppBundle:widgets/product/wish_button.html.twig', [
                'item' => $item,
                'addedFlag' => $addedFlag,
                'autoRemove' => Arr::get($params, 'autoRemove')
            ]
        );
    }

    /**
     * Render wish counter
     *
     * @return mixed
     */
    public function wishCounter()
    {
        return $this->templating->render('AppBundle:widgets/product/wish_counter.html.twig', [
                'count' => $this->container->get('wishlist')->getCount(),
            ]
        );
    }

    /**
     * Render recently reviewed
     *
     * @return mixed
     */
    public function recentlyReviewed()
    {
        return $this->templating->render('AppBundle:widgets/product/recently_reviewed.html.twig', [
                'recentModels' => $this->container->get('entities')->getRecentlyViewed()
            ]
        );
    }

    /**
     * render model recommended
     *
     * @param ProductModels $productModels
     * @return mixed
     */
    public function recommended(ProductModels $productModels)
    {
        return $this->templating->render('AppBundle:widgets/product/recommended.html.twig', [
                'recommended' => $productModels->getRecommended()
            ]
        );
    }

    /**
     * render model recommended
     *
     * @param ProductModels $model
     * @return mixed
     */
    public function upsell(ProductModels $model)
    {
        if ($this->container->get('share')->isActualUpSellShare($model->getShare())) {
            $priceCalculator = $this->container->get('prices_calculator');
            $currentShareGroup = $model->getSizes()->first()->getShareGroup();
            $currentSize = $model->getSizes()->first();
            $sizesGroups = $model->getShare()->getSizesGroups();

            // Main group upSell
            $totalSum = $priceCalculator->getProductModelSpecificSizeUpSellDiscountedPrice($model->getSizes()->first());
            $sizesGroups = $sizesGroups->getValues();
            $upSellGroups = array_filter($sizesGroups, function ($group) use ($currentShareGroup) {
                return $group->getId() != $currentShareGroup->getId();
            });

            // Select one product from each group
            $allUpSellSizes = array_map(function (ShareSizesGroup $sizesGroup) use($priceCalculator) {
                $sizes = $sizesGroup->getModelSpecificSizes()->getValues();
                return $sizes[array_rand($sizes)];
            }, $upSellGroups);

            $upSell = array_map(function (ProductModelSpecificSize $size) use($priceCalculator) {
                return ['obj' => $size, 'discount' => $size->getShareGroup()->getDiscount()];
            }, $allUpSellSizes);

            $totalSum += round(array_sum(array_map(function ($size) use($priceCalculator) {
                return $priceCalculator->getProductModelSpecificSizeUpSellDiscountedPrice($size);
            }, $allUpSellSizes)));

            // Create form
            $allUpSellSizes[] = $currentSize;
            $form = $this->container->get('form.factory')->create(AddUpSellInCartType::class, null, [
                'action' => $this->container->get('router')->generate('cart_add_many'),
                'sizes' => $allUpSellSizes
            ])->createView();

            $currentDiscount = $currentShareGroup->getDiscount();
            $companionFlag = false;

            $upSells[] = compact('upSell', 'totalSum', 'form', 'currentDiscount', 'companionFlag');


            // Combinations upSell
            foreach ($sizesGroups as $group) {
                // Protect combination duplicates
                if (count($sizesGroups) == 2 && $group->getId() != $currentShareGroup->getId()) {
                    continue;
                }
                $discount = $this->container->get('share')->discountValueForShareGroupCompanion($currentShareGroup,
                    $group);
                $companionDiscount = $this->container->get('share')->discountValueForShareGroupCompanion($group,
                    $currentShareGroup);
                if ($discount || $companionDiscount) {
                    // When discount 1 + 1 for same group - select same size
                    if ($group->getId() == $currentShareGroup->getId()) {
                        $companion = $currentSize;
                    } else {
                        $sizes = $group->getModelSpecificSizes()->getValues();
                        $companion = $sizes[array_rand($sizes)];
                    }
                    $sizePrice = $priceCalculator->getProductModelSpecificSizeUpSellWithCompanionDiscountedPrice($currentSize,
                        $companion);
                    $companionPrice = $priceCalculator->getProductModelSpecificSizeUpSellWithCompanionDiscountedPrice($companion,
                        $currentSize);

                    $form = $this->container->get('form.factory')->create(AddUpSellInCartType::class, null, [
                        'action' => $this->container->get('router')->generate('cart_add_many'),
                        'sizes' => [$currentSize, $companion]
                    ])->createView();

                    $upSells[] = [
                        'companion' => $companion,
                        'companionDiscount' => $companionDiscount,
                        'totalSum' => $sizePrice + $companionPrice,
                        'form' => $form,
                        'sizePrice' => $sizePrice,
                        'companionPrice' => $companionPrice,
                        'currentDiscount' => $discount,
                        'companionFlag' => true
                    ];
                }
            }

            return $this->templating->render(
                'AppBundle:widgets/product/upsells.html.twig',
                compact('upSells', 'model', 'currentSize')
            );
        }
        
        return null;
    }

    /**
     * Render old and new prices
     *
     * @param $object
     * @return mixed
     */
    public function prices($object)
    {
        $oldPrice = 0;
        if ($object instanceof ProductModels) {
            $size = $this->container->get('prices_calculator')->getProductModelLowestSpecificSize($object);
            $price = $size->getPrice();
            $oldPrice = $size->getOldPrice();
            $discountedPrice = $this->container->get('prices_calculator')->getProductModelLowestSpecificSizeDiscountedPrice($object);
        } elseif ($object instanceof CartSize) {
            // CartSize instance contain right calculated prices
            $price = $object->getPrice();
            $discountedPrice = $object->getDiscountedPrice();
        } else {
            $price = $this->container->get('prices_calculator')->getPrice($object);
            $discountedPrice = $this->container->get('prices_calculator')->getDiscountedPrice($object);
        }
        return $this->templating->render('AppBundle:widgets/product/prices.html.twig', [
                'object' => $object,
                'price' => $price,
                'discountedPrice' => $discountedPrice,
                'oldPrice' => $oldPrice,
            ]
        );
    }

//    /**
//     * Render old and new prices
//     *
//     * @param $object
//     * @return mixed
//     */
//    public function oldPrices($object)
//    {
//        $oldPrice = $this->container->get('prices_calculator')->getProductModelLowestSpecificSizeOldPrice($object);
//
//        return $this->templating->render('AppBundle:widgets/product/old_prices.html.twig', [
//                'oldPrice' => $oldPrice,
//            ]
//        );
//    }

    /**
     * @param $model
     * @return mixed
     */
    public function flags(ProductModels $model)
    {
        if($flagText = $model->getTextLabel()) {
            $flag = $model->getTextLabelColor() ? false : 'discount';
        } else if($discount = $this->container->get('share')->getSingleDiscount($model)) {
            $flag = 'discount';
            $flagText = $discount . '%';
        } else {
            return '';
        }

        return $this->templating->render('AppBundle:widgets/product/flags.html.twig',
            compact('model', 'flag', 'flagText'));
    }

    /**
     * @param $model
     * @return mixed
     */
    public function addInCart($model)
    {
        $form = $this->container->get('form.factory')->create(AddInCartType::class, null, [
            'model' => $model
        ])->add('quantity', HiddenType::class, [
            'data' => 1,
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/add_in_cart.html.twig', [
                'model' => $model,
                'form' => $form,
            ]
        );
    }

    /**
     * @param CartSize $size
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function changeProductSize(CartSize $size)
    {
        $form = $this->container->get('form.factory')->create(ChangeProductSizeType::class, null, [
            'action' => $this->container->get('router')->generate('cart_change_size', ['id' => $size->getSize()->getId()]),
            'size' => $size->getSize(),
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/change_product_size.html.twig', [
                'model' => $size->getSize()->getModel(),
                'form' => $form,
            ]
        );
    }

    /**
     * @param CartSize $selectedSize
     * @param bool $preOrderFlag
     * @return mixed
     */
    public function changeProductSizeCount(CartSize $selectedSize, $preOrderFlag = false)
    {
        $form = $this->container->get('form.factory')->create(ChangeProductSizeQuantityType::class, null, [
            'action' => $this->container->get('router')->generate('cart_change_size_count',
                ['id' => $selectedSize->getSize()->getId()]),
            'size' => $selectedSize->getSize(),
            'selected' => $preOrderFlag ? $selectedSize->getPreOrderQuantity() : $selectedSize->getStandardQuantity(),
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/change_product_size_count.html.twig', [
                'size' => $selectedSize->getSize(),
                'form' => $form,
            ]
        );
    }

    /**
     * @param CartSize $size
     * @param $preOrderMode
     * @return mixed
     */
    public function removeProductFromCart(CartSize $size, $preOrderMode)
    {
        $form = $this->container->get('form.factory')->create(ChangeProductSizeQuantityType::class, null, [
            'action' => $this->container->get('router')->generate('cart_change_size_count',
                ['id' => $size->getSize()->getId()]),
            'size' => $size->getSize(),
            'toChangeQuantity' => - ($preOrderMode ? $size->getPreOrderQuantity() : $size->getStandardQuantity()),
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/remove_product_size.html.twig', [
                'form' => $form,
            ]
        );
    }


}
