<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\ProductModels;
use AppBundle\Form\Type\ChangeProductSizeQuantityType;
use AppBundle\Form\Type\ChangeProductSizeType;
use AppBundle\Form\Type\RemoveProductSizeType;
use Doctrine\ORM\EntityManager;
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
     * Render list item
     *
     * @param ProductModels $item
     * @return mixed
     */
    public function wishButton(ProductModels $item)
    {
        $addedFlag = in_array($item->getId(), $this->container->get('wishlist')->getIds());

        return $this->templating->render('AppBundle:widgets/product/wish_button.html.twig', array(
                'item' => $item,
                'addedFlag' => $addedFlag,
            )
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
     * Render recently reviewed
     *
     * @return mixed
     */
    public function recommended()
    {
        return $this->templating->render('AppBundle:widgets/product/recommended.html.twig', [
                // todo select render recently reviewed from database
            ]
        );
    }

    /**
     * Render old and new prices
     *
     * @param $model
     * @return mixed
     */
    public function prices($model)
    {
        return $this->templating->render('AppBundle:widgets/product/prices.html.twig', [
                'model' => $model
            ]
        );
    }

    /**
     * @param $model
     * @return mixed
     */
    public function flags($model)
    {
        // todo add actions and other flags

        echo 'getPreorderFlag ';
        echo((int)$model->getPreorderFlag());
        echo 'getPreorderFlag ';
        if ($model->getPreorderFlag()) {
            $flag = 'soon';
            $flagText = $this->container->get('translator')->trans('Pre order product flag');
        } else {
            $flag = $flagText = false;
        }

        return $this->templating->render('AppBundle:widgets/product/flags.html.twig', compact('model', 'flag', 'flagText'));
    }

    /**
     * @param $model
     * @return mixed
     */
    public function addInCart($model)
    {
        $skuProduct = $model->getSkuProducts()->first();

        $form = $this->container->get('form.factory')->create(AddInCartType::class, null, [
            'action' => $this->container->get('router')->generate('cart_add', ['id' => $skuProduct->getId()]),
            'model' => $model
        ])->add('quantity', HiddenType::class, [
            'data' => 1,
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/add_in_cart.html.twig', array(
                'model' => $model,
                'form' => $form,
            )
        );
    }

    /**
     * @param $model
     * @param $selectedSize
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function changeProductSize($model, $selectedSize)
    {
        $skuProduct = $model->getSkuProducts()->first();

        $form = $this->container->get('form.factory')->create(ChangeProductSizeType::class, null, [
            'action' => $this->container->get('router')->generate('cart_change_size', ['id' => $skuProduct->getId()]),
            'model' => $model,
            'selectedSize' => $this->em->getReference("AppBundle:ProductModelSizes", $selectedSize) ,
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/change_product_size.html.twig', array(
                'model' => $model,
                'form' => $form,
            )
        );
    }

    /**
     * @param $model
     * @param $selectedSize
     * @param $count
     * @return mixed
     * @throws \Doctrine\ORM\ORMException
     */
    public function changeProductSizeCount($model, $selectedSize, $count)
    {
        $skuProduct = $model->getSkuProducts()->first();

        $form = $this->container->get('form.factory')->create(ChangeProductSizeQuantityType::class, null, [
            'action' => $this->container->get('router')->generate('cart_change_size_count', ['id' => $skuProduct->getId()]),
            'size' => $selectedSize,
            'selected' => $count,
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/change_product_size_count.html.twig', array(
                'skuProduct' => $skuProduct,
                'form' => $form,
            )
        );
    }

    /**
     * @param $skuProduct
     * @param $size
     * @return mixed
     */
    public function removeProductFromCart($skuProduct, $size)
    {
        $form = $this->container->get('form.factory')->create(RemoveProductSizeType::class, null, [
            'action' => $this->container->get('router')->generate('cart_remove', ['id' => $skuProduct->getId()]),
            'size' => $size
        ])->createView();

        return $this->templating->render('AppBundle:widgets/product/remove_product_size.html.twig', array(
                'form' => $form,
            )
        );
    }


}
