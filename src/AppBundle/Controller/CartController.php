<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SkuProducts;
use AppBundle\Form\Type\AddInCartType;
use AppBundle\Form\Type\ChangeProductSizeQuantityType;
use AppBundle\Form\Type\ChangeProductSizeType;
use AppBundle\Form\Type\CreateOrderType;
use AppBundle\Form\Type\QuickOrderType;
use AppBundle\Form\Type\RemoveProductSizeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CartController extends BaseController
{

    /**
     * @Route("/cart/add/{id}", name="cart_add", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param SkuProducts $skuProduct
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function addInCartAction(SkuProducts $skuProduct, Request $request)
    {
        $form = $this->createForm(AddInCartType::class, null, ['model' => $skuProduct->getProductModels()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->addItemToCard(
                $skuProduct,
                $form->get('size')->getNormData(),
                $form->get('quantity')->getNormData()
            );

            if ($form->get('submit')->isClicked()) {
                return $this->redirect($request->headers->get('referer'));
            }
            return new JsonResponse([
                'totalCount' => $this->get('cart')->getTotalCount(),
                'totalPrice' => $this->get('cart')->getTotalPrice()
            ]);
        }

        if ($form->get('submit')->isClicked()) {
            return $this->redirect($request->headers->get('referer'));
        }
        return new JsonResponse(['errors' => $this->formErrorsToArray($form)], 422);
    }

    /**
     * @Route("/cart/add_wholesale", name="cart_add_wholesale", options={"expose"=true})
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function addInCartWholesaleAction(Request $request)
    {
        $products = $request->request->get('products', []);

        foreach ($products as $productId => $sizes) {
            foreach ($sizes as $sizeId => $quantity) {
                $this->get('cart')->addItemToCard(
                    $this->getDoctrine()->getManager()->getReference('AppBundle:SkuProducts', $productId),
                    $sizeId,
                    $quantity
                );
            }
        }

        return new JsonResponse([
            'totalCount' => $this->get('cart')->getTotalCount(),
            'totalOriginalPrice' => $this->get('cart')->getTotalOldPrice(),
            'totalPrice' => $this->get('cart')->getTotalPrice(),
            'singleItemsCount' => $this->get('cart')->getSingleItemsCount(),
            'packagesCount' => $this->get('cart')->getPackagesCount(),
            'cartItems' => $this->get('cart')->toArrayWithExtraInfo(),
            'preOrderItemsPrice' => $this->get('cart')->getPreOrderItemsPrice(),
            'standardItemsPrice' => $this->get('cart')->getStandardItemsPrice(),
        ]);
    }

    /**
     * @Route("/cart/change_size/{id}", name="cart_change_size", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param SkuProducts $skuProduct
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function changeSizeAction(SkuProducts $skuProduct, Request $request)
    {
        $form = $this->createForm(ChangeProductSizeType::class, null, ['model' => $skuProduct->getProductModels()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->changeItemSize(
                $skuProduct,
                $form->get('old_size')->getNormData(),
                $form->get('size')->getNormData()->getId()
            );

            return new JsonResponse([
                'totalCount' => $this->get('cart')->getTotalCount(),
                'totalPrice' => $this->get('cart')->getTotalPrice()
            ]);
        }

        return new JsonResponse(['errors' => $this->formErrorsToArray($form)], 422);
    }

    /**
     * @Route("/cart/change_size_count/{id}", name="cart_change_size_count", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param SkuProducts $skuProduct
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function changeSizeCountAction(SkuProducts $skuProduct, Request $request)
    {
        $form = $this->createForm(ChangeProductSizeQuantityType::class, null, ['size' => $skuProduct->getProductModels()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->changeItemSizeCount(
                $skuProduct,
                $form->get('size')->getNormData(),
                $form->get('quantity')->getNormData()
            );
            $cartInfo = $this->getGeneralCartInfo();
            $cartInfo['currentPrice'] = $this->get('cart')->getItem($skuProduct)->getPrice();
            return new JsonResponse($cartInfo);
        }

        return new JsonResponse(['errors' => $this->formErrorsToArray($form)], 422);
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param SkuProducts $skuProduct
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function cartRemove(SkuProducts $skuProduct, Request $request)
    {
        $form = $this->createForm(RemoveProductSizeType::class, null, ['size' => $skuProduct->getProductModels()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->removeItemSize($skuProduct, $form->get('size')->getNormData());

            return new JsonResponse($this->getGeneralCartInfo());
        }

        return new JsonResponse(['errors' => $this->formErrorsToArray($form)], 422);
    }

    /**
     * @Route("/cart/show", name="cart_show", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showCartAction(Request $request)
    {
        return $this->render('AppBundle:shop:cart/show.html.twig', [
            'continueShopUrl' => $this->get('last_urls')->getLastCatalogUrl()
        ]);
    }

    /**
     * @Route("/cart/order", name="cart_order", options={"expose"=true})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showOrderAction(Request $request)
    {
        $orderForm = $this->createForm(CreateOrderType::class, null, [
            'request' => $request->request,
            'user' => $this->getUser()
        ]);

        $orderForm->handleRequest($request);
        if ($orderForm->isValid()) {
            // todo when no user - create new
            $order = $this->get('cart')->flushCart($this->getUser(), $orderForm->getData());

            return $this->render('AppBundle:shop:cart/order_approve.html.twig', [
                'order' => $order,
                'continueShopUrl' => $this->get('last_urls')->getLastCatalogUrl()
            ]);
        }

        $quickOrderForm = $this->createForm(QuickOrderType::class, null, [
            'action' => $this->container->get('router')->generate('cart_quick_order')
        ]);

        return $this->render('AppBundle:shop:cart/order.html.twig', [
            'quickOrderForm' => $quickOrderForm->createView(),
            'orderForm' => $orderForm->createView()
        ]);
    }

    /**
     * @Route("/cart/quick_order", name="cart_quick_order", options={"expose"=true})
     * @Method("POST")
     * @param Request $request
     */
    public function quickOrderAction(Request $request)
    {
        // todo here is only beginning, complete
        $this->get('cart')->createQuickOrder();

        $form = $this->createForm(QuickOrderType::class);
    }

    /**
     * @Route("/cart/quick_order/{id}", name="cart_quick_order_single_product", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param SkuProducts $skuProducts
     * @param Request $request
     */
    public function quickOrderSingleProductAction(SkuProducts $skuProducts, Request $request)
    {
        // todo here is only beginning, complete
        $this->get('cart')->clear();
        $this->get('cart')->addInCart($skuProducts);
        $this->get('cart')->createQuickOrder();
    }

    /**
     * @return array
     */
    protected function getGeneralCartInfo()
    {
        return [
            // todo add discount, total oldPrice
            'originalItemsPrice' => $this->get('cart')->getTotalOldPrice(),
            'preOrderItemsPrice' => $this->get('cart')->getPreOrderItemsPrice(),
            'standardItemsPrice' => $this->get('cart')->getStandardItemsPrice(),
            'totalCount' => $this->get('cart')->getTotalCount(),
            'totalPrice' => $this->get('cart')->getTotalPrice()
        ];
    }

}

