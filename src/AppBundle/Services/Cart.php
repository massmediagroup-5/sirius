<?php

namespace AppBundle\Services;

use AppBundle\Entity\ProductModelSizes;
use AppBundle\Entity\SkuProducts;
use AppBundle\Model\CartItem;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: Cart
 * @author R. Slobodzian
 */
class Cart
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * session
     *
     * @var mixed
     */
    private $session;

    /**
     * Items objects list
     *
     * @var CartItem[]
     */
    private $items = [];

    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     * @param Session $session
     */
    public function __construct(EntityManager $em, ContainerInterface $container, Session $session)
    {
        $this->em = $em;
        $this->container = $container;
        $this->session = $session;
        $this->initCartFromSession();
    }

    /**
     * @param $skuProduct
     * @param $size
     * @param $quantity
     */
    public function addItemToCard($skuProduct, $size, $quantity)
    {
        $sizeId = $size instanceof ProductModelSizes ? $size->getId() : $size;

        if (isset($this->items[$skuProduct->getId()])) {
            // Each size quantity
            $this->items[$skuProduct->getId()]->addSize($sizeId, $quantity);
        } else {
            $this->items[$skuProduct->getId()] = new CartItem($skuProduct);
            $this->items[$skuProduct->getId()]->addSize($sizeId, $quantity);
        }
        $this->saveInSession();
    }

    /**
     * @param $skuProduct
     * @return $this
     */
    public function removeItem($skuProduct)
    {
        unset($this->items[$skuProduct->getId()]);
        $this->saveInSession();
        return $this;
    }

    /**
     * @param $skuProduct
     * @param $size
     * @return $this
     */
    public function removeItemSize($skuProduct, $size)
    {
        $this->items[$skuProduct->getId()]->removeSize($size);
        if(!$this->items[$skuProduct->getId()]->getQuantity()) {
            $this->removeItem($skuProduct);
        }
        $this->saveInSession();
        return $this;
    }

    /**
     * @param $skuProduct
     * @param $oldSize
     * @param $newSize
     * @return $this
     */
    public function changeItemSize($skuProduct, $oldSize, $newSize)
    {
        $this->items[$skuProduct->getId()]->changeSize($oldSize, $newSize);
        $this->saveInSession();
        return $this;
    }

    /**
     * @param $skuProduct
     * @param $size
     * @param $quantity
     * @return $this
     */
    public function changeItemSizeCount($skuProduct, $size, $quantity)
    {
        $this->items[$skuProduct->getId()]->setSize($size, $quantity);
        $this->saveInSession();
        return $this;
    }

    /**
     * @param $skuProduct
     * @return bool
     */
    public function inCart($skuProduct)
    {
        $skuProductId = $skuProduct instanceof SkuProducts ? $skuProduct->getId() : $skuProduct;

        return isset($this->items[$skuProductId]);
    }

    /**
     * @return CartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param SkuProducts $productSku
     * @return CartItem[]
     */
    public function getItem(SkuProducts $productSku)
    {
        return isset($this->items[$productSku->getId()]) ? $this->items[$productSku->getId()] : false;
    }

    /**
     * @return CartItem[]
     */
    public function getPreOrderItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return $item->getSkuProduct()->getProductModels()->getPreOrderFlag();
        });
    }

    /**
     * @return CartItem[]
     */
    public function getStandardItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return !$item->getSkuProduct()->getProductModels()->getPreOrderFlag();
        });
    }

    /**
     * @return CartItem[]
     */
    public function getStandardItemsPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPrice();
        }, $this->getStandardItems()));
    }

    /**
     * @return CartItem[]
     */
    public function getPreOrderItemsPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPrice();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return array_sum(array_map(function ($item) {
            return $item->getQuantity();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getPreOrderCount()
    {
        return array_sum(array_map(function ($item) {
            return $item->getSkuProduct()->getProductModels()->getPreOrderFlag() ? array_sum($item->getQuantity()) : 0;
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getTotalPrice()
    {
        return array_sum(array_map(function ($item) {
            return $item->getPrice();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getTotalOldPrice()
    {
        return array_sum(array_map(function ($item) {
            return $item->getOldPrice();
        }, $this->items));
    }

    /**
     * @return void
     */
    public function saveInSession()
    {
        $sessionArray = [];
        foreach ($this->items as $item) {
            $sessionArray[$item->getSkuProduct()->getId()] = [
                'id' => $item->getSkuProduct()->getId(),
                'sizes' => $item->getSizes()
            ];
        }
        $this->session->set('cart_items', $sessionArray);
    }

    /**
     * @return void
     */
    protected function initCartFromSession()
    {
        $sessionArray = $this->session->get('cart_items', []);
        $skuProducts = $this->em->getRepository('AppBundle:SkuProducts')->findWithModels(array_keys($sessionArray));
        foreach ($skuProducts as $skuProduct) {
            $cartItem = new CartItem($skuProduct);
            $cartItem->setSizes($sessionArray[$skuProduct->getId()]['sizes']);
            $this->items[$skuProduct->getId()] = $cartItem;
        }
        $this->session->set('cart_items', $sessionArray);
    }

}
