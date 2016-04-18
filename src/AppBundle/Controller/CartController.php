<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductModelSpecificSize;
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
     * @param ProductModelSpecificSize $size
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function addInCartAction(ProductModelSpecificSize $size, Request $request)
    {
        $form = $this->createForm(AddInCartType::class, null, ['model' => $size->getModel()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->addItemToCard(
                $size,
                $form->get('quantity')->getNormData()
            );

            if ($form->get('submit')->isClicked()) {
                return $this->redirect($request->headers->get('referer'));
            }
            return new JsonResponse([
                'totalCount' => $this->get('cart')->getTotalCount(),
                'discountedTotalPrice' => $this->get('cart')->getDiscountedTotalPrice()
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
                    $this->getDoctrine()->getManager()->getReference('AppBundle:ProductModelSpecificSize', $sizeId),
                    $quantity
                );
            }
        }

        return new JsonResponse($this->getGeneralCartInfoWholesale());
    }

    /**
     * @Route("/cart/change_size/{id}", name="cart_change_size", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param ProductModelSpecificSize $size
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function changeSizeAction(ProductModelSpecificSize $size, Request $request)
    {
        $form = $this->createForm(ChangeProductSizeType::class, null, ['model' => $size->getModel()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->changeItemSize(
                $size,
                $form->get('size')->getNormData()
            );

            return new JsonResponse([
                'totalCount' => $this->get('cart')->getTotalCount(),
                'discountedTotalPrice' => $this->get('cart')->getDiscountedTotalPrice()
            ]);
        }

        return new JsonResponse(['errors' => $this->formErrorsToArray($form)], 422);
    }

    /**
     * @Route("/cart/change_size_count/{id}", name="cart_change_size_count", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param ProductModelSpecificSize $size
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function changeSizeCountAction(ProductModelSpecificSize $size, Request $request)
    {
        $form = $this->createForm(ChangeProductSizeQuantityType::class, null, ['size' => $size->getModel()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->changeItemSizeCount(
                $size,
                $form->get('quantity')->getNormData()
            );
            $cartInfo = $this->getGeneralCartInfo();
            $cartInfo['currentPrice'] = $this->get('cart')->getItem($size->getModel())->getPrice();
            return new JsonResponse($cartInfo);
        }

        return new JsonResponse(['errors' => $this->formErrorsToArray($form)], 422);
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param ProductModelSpecificSize $size
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function cartRemove(ProductModelSpecificSize $size, Request $request)
    {
        $form = $this->createForm(RemoveProductSizeType::class, null, ['size' => $size->getModel()]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($form->has('size')) {
                $this->get('cart')->removeItemSize($size, $form->get('size')->getNormData());
            } else {
                $this->get('cart')->removeItem($size->getModel());
            }

            return new JsonResponse($this->getGeneralCartInfo());
        }

        return new JsonResponse(['errors' => $this->formErrorsToArray($form)], 422);
    }

    /**
     * @Route("/cart/remove_item/{id}", name="cart_remove_item", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param ProductModelSpecificSize $size
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function cartRemoveItem(ProductModelSpecificSize $size, Request $request)
    {
        $this->get('cart')->removeItem($size->getModel());

        if ($this->isGranted('ROLE_WHOLESALER')) {
            return new JsonResponse($this->getGeneralCartInfoWholesale());
        }

        return new JsonResponse($this->getGeneralCartInfo());
    }

    /**
     * @Route("/cart/remove_sizes", name="cart_remove_sizes", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function cartRemoveSizes(Request $request)
    {
        foreach ($request->get('sizes', []) as $sizeId) {
            $this->get('cart')->removeItemSize($this->getDoctrine()->getManager()->getReference('AppBundle:ProductModelSpecificSize',
                $sizeId));
        }

        if ($this->isGranted('ROLE_WHOLESALER')) {
            return new JsonResponse($this->getGeneralCartInfoWholesale());
        }

        return new JsonResponse($this->getGeneralCartInfo());
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
            'orderForm' => $orderForm->createView(),
            'orderFormSubmitFlag' => $orderForm->isSubmitted(),
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
     * @param ProductModelSpecificSize $size
     * @param Request $request
     */
    public function quickOrderSingleProductAction(ProductModelSpecificSize $size, Request $request)
    {
        // todo here is only beginning, complete
        $this->get('cart')->clear();
        $this->get('cart')->addInCart($size);
        $this->get('cart')->createQuickOrder();
    }

    /**
     * @return array
     */
    protected function getGeneralCartInfo()
    {
        return [
            // todo add discount, total oldPrice
            'totalPrice' => $this->get('cart')->getTotalPrice(),
            'preOrderItemsPrice' => $this->get('cart')->getPreOrderItemsPrice(),
            'standardItemsPrice' => $this->get('cart')->getStandardItemsPrice(),
            'totalCount' => $this->get('cart')->getTotalCount(),
            'discountedTotalPrice' => $this->get('cart')->getDiscountedTotalPrice()
        ];
    }

    /**
     * @return array
     */
    protected function getGeneralCartInfoWholesale()
    {
        return [
            'totalCount' => $this->get('cart')->getTotalCount(),
            'totalPrice' => $this->get('cart')->getTotalPrice(),
            'discountedTotalPrice' => $this->get('cart')->getDiscountedTotalPrice(),
            'singleItemsCount' => $this->get('cart')->getSingleItemsCount(),
            'packagesCount' => $this->get('cart')->getPackagesCount(),
            'cartItems' => $this->get('cart')->toArrayWithExtraInfo(),
            'preOrderItemsPrice' => $this->get('cart')->getPreOrderItemsPrice(),
            'standardItemsPrice' => $this->get('cart')->getStandardItemsPrice(),
        ];
    }

}

