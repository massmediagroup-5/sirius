<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Form\Type\AddInCartType;
use AppBundle\Form\Type\AddUpSellInCartType;
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
     * @Route("/cart/add_many", name="cart_add_many", options={"expose"=true})
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function addManyInCartAction(Request $request)
    {
        $form = $this->createForm(AddUpSellInCartType::class, null);
        $form->handleRequest($request);

        if ($form->isValid()) {
            foreach ($form->get('sizes')->getData() as $size) {
                $sizeEntity = $this->getDoctrine()->getManager()->getRepository('AppBundle:ProductModelSpecificSize')
                    ->find($size);
                $this->get('cart')->addItemToCard($sizeEntity, 1);
            }

            return new JsonResponse([
                'totalCount' => $this->get('cart')->getTotalCount(),
                'discountedTotalPrice' => $this->get('cart')->getDiscountedTotalPrice(),
                'messages' => ['Добавлено в корзину']
            ]);
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
        $form = $this->createForm(ChangeProductSizeType::class, null, ['size' => $size]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('cart')->changeItemSize(
                $size,
                $form->get('size')->getNormData()
            );

            $cartInfo = $this->getGeneralCartInfo();
            $cartInfo['currentPrice'] = $this->get('cart')->getSizeDiscountedPrice($form->get('size')->getNormData());
            return new JsonResponse($cartInfo);
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
            $cartInfo['currentPrice'] = $this->get('cart')->getSizeDiscountedPrice($size);
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
        if($this->get('cart')->isEmpty()) {
            return $this->forward('AppBundle:Cart:showCart');
        }

        $orderForm = $this->createForm(CreateOrderType::class, null, [
            'request' => $request->request,
            'user' => $this->getUser()
        ]);

        $orderForm->handleRequest($request);
        if ($orderForm->isValid()) {

            $order = $this->get('order')->orderFromCart($orderForm->getData(), $this->getUser());

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'redirect' => $this->container->get('router')->generate('cart_order_approve', ['id' => $order])
                ]);
            }

            return $this->render('AppBundle:shop:cart/order_approve.html.twig', [
                'order' => $order,
                'continueShopUrl' => $this->get('last_urls')->getLastCatalogUrl()
            ]);
        }

        if($request->isXmlHttpRequest() && $orderForm->isSubmitted()) {
            return new JsonResponse(['messages' => ['Не все поля заполнены!'], 'errors' => $this->getErrorsAsArray($orderForm)], 422);
        }


        $quickOrderForm = $this->createForm(QuickOrderType::class, null);

        $quickOrderForm->handleRequest($request);
        if ($quickOrderForm->isValid()) {

            $order = $this->get('order')->orderFromCart($quickOrderForm->getData(), $this->getUser(), true);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse([
                    'redirect' => $this->container->get('router')->generate('cart_order_approve', ['id' => $order])
                ]);
            }

            return $this->render('AppBundle:shop:cart/order_approve.html.twig', [
                'order' => $order,
                'continueShopUrl' => $this->get('last_urls')->getLastCatalogUrl()
            ]);
        }

        if($request->isXmlHttpRequest() && $quickOrderForm->isSubmitted()) {
            return new JsonResponse(['messages' => ['Не все поля заполнены!'], 'errors' => $this->getErrorsAsArray($quickOrderForm)], 422);
        }

        return $this->render('AppBundle:shop:cart/order.html.twig', [
            'quickOrderForm' => $quickOrderForm->createView(),
            'orderForm' => $orderForm->createView(),
            'orderFormSubmitFlag' => $orderForm->isSubmitted(),
        ]);
    }

    /**
     * @Route("/cart/order/{id}/approve", name="cart_order_approve", options={"expose"=true})
     * @ParamConverter("model")
     * @param Orders $order
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function orderApproveAction(Orders $order)
    {
        return $this->render('AppBundle:shop:cart/order_approve.html.twig', [
            'order' => $order,
            'continueShopUrl' => $this->get('last_urls')->getLastCatalogUrl()
        ]);
    }

    /**
     * @Route("/cart/quick_order/{id}", name="cart_quick_order_single_product", options={"expose"=true})
     * @Method("POST")
     * @ParamConverter("model")
     * @param ProductModelSpecificSize $size
     * @param Request $request
     * @return JsonResponse
     */
    public function quickOrderSingleProductAction(ProductModelSpecificSize $size, Request $request)
    {
        $quickOrderForm = $this->createForm(QuickOrderType::class, null);

        $quickOrderForm->handleRequest($request);
        if ($quickOrderForm->isValid()) {
            $this->get('cart')->backupCart();
            $this->get('cart')->clear();
            $this->get('cart')->addItemToCard($size, 1);
            $this->get('order')->orderFromCart($quickOrderForm->getData(), $this->getUser(), true);
            $this->get('cart')->restoreCartFromBackup();

            return new JsonResponse();
        }

        return new JsonResponse([], 422);
    }

    /**
     * @return array
     */
    protected function getGeneralCartInfo()
    {
        return [
            // todo add discount, total oldPrice
            'totalPrice' => $this->get('cart')->getTotalPrice(),
            'preOrderItemsPrice' => $this->get('cart')->getPreOrderDiscountedPrice(),
            'standardItemsPrice' => $this->get('cart')->getStandardDiscountedPrice(),
            'totalCount' => $this->get('cart')->getTotalCount(),
            'totalDiscount' => $this->get('cart')->getDiscount(),
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
            'totalDiscount' => $this->get('cart')->getDiscount(),
            'discountedTotalPrice' => $this->get('cart')->getDiscountedTotalPrice(),
            'singleItemsCount' => $this->get('cart')->getSingleItemsCount(),
            'packagesCount' => $this->get('cart')->getPackagesCount(),
            'cartItems' => $this->get('cart')->toArrayWithExtraInfo(),
            'preOrderItemsPrice' => $this->get('cart')->getPreOrderDiscountedPrice(),
            'standardItemsPrice' => $this->get('cart')->getStandardDiscountedPrice(),
        ];
    }

    /**
     * @param $form
     * @return array
     */
    public function getErrorsAsArray($form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $key => $child) {
            if ($err = $this->getErrorsAsArray($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
    }

}

