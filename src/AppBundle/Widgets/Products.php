<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ShareSizesGroup;
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
            $currentShareGroup = $model->getSizes()->first()->getShareGroup();

            $sizesGroups = $model->getShare()->getSizesGroups()->getValues();
            $upSellGroups = array_filter($sizesGroups, function ($group) use ($currentShareGroup) {
                return $group->getId() != $currentShareGroup->getId();
            });
            // Select one product from each group
            $upSell = array_map(function (ShareSizesGroup $sizesGroup) {
                $products = $sizesGroup->getProducts()->getValues();
                return $products[array_rand($products)];
            }, $upSellGroups);

            // TODO render upsell items

            return $this->templating->render('AppBundle:widgets/product/upsell.html.twig', compact('model', 'upSell'));
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
        if ($object instanceof ProductModels) {
            $price = $this->container->get('prices_calculator')->getProductModelLowestSpecificSizePrice($object);
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
                'oldPrice' => $discountedPrice * $this->container->get('options')->getParamValue('old_price_koef'),
            ]
        );
    }

    /**
     * @param $model
     * @return mixed
     */
    public function flags(ProductModels $model)
    {
        if($discount = $this->container->get('share')->getSingleDiscount($model)) {
            $flag = 'discount';
            $flagText = $discount . '%';
        } else {
            $flag = $flagText = false;
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
     * @param $selectedSize
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function changeProductSizeCount(CartSize $selectedSize)
    {
        $form = $this->container->get('form.factory')->create(ChangeProductSizeQuantityType::class, null, [
            'action' => $this->container->get('router')->generate('cart_change_size_count',
                ['id' => $selectedSize->getSize()->getId()]),
            'size' => $selectedSize->getSize(),
            'selected' => $selectedSize->getQuantity(),
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/change_product_size_count.html.twig', [
                'size' => $selectedSize->getSize(),
                'form' => $form,
            ]
        );
    }

    /**
     * @param $size
     * @return mixed
     */
    public function removeProductFromCart(CartSize $size)
    {
        $form = $this->container->get('form.factory')->create(RemoveProductSizeType::class, null, [
            'action' => $this->container->get('router')->generate('cart_remove', ['id' => $size->getSize()->getId()]),
            'size' => $size->getSize()
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/remove_product_size.html.twig', [
                'form' => $form,
            ]
        );
    }


}
