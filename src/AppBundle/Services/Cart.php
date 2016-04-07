<?php

namespace AppBundle\Services;

use AppBundle\Entity\CartProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModelSizes;
use AppBundle\Entity\SkuProducts;
use AppBundle\Entity\Users as UsersEntity;
use AppBundle\Event\OrderEvent;
use AppBundle\Exception\CartEmptyException;
use AppBundle\Model\CartItem;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
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
            $this->items[$skuProduct->getId()] = new CartItem($skuProduct, $this->container->get('prices_calculator'));
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
     * @return $this
     */
    public function clear()
    {
        $this->items = [];
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
        if (!$this->items[$skuProduct->getId()]->getQuantity()) {
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
     * @return CartItem[]
     */
    public function getModels()
    {
        return array_map(function (CartItem $item) {
            return $item->getSkuProduct()->getProductModels();
        }, $this->items);
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
     * @param SkuProducts $productSku
     * @return int
     */
    public function getItemQuantity(SkuProducts $productSku)
    {
        return isset($this->items[$productSku->getId()]) ? $this->items[$productSku->getId()]->getQuantity() : 0;
    }

    /**
     * @param SkuProducts $productSku
     * @return int
     */
    public function getItemPackagesQuantity(SkuProducts $productSku)
    {
        return isset($this->items[$productSku->getId()]) ? $this->items[$productSku->getId()]->getPackagesQuantity() : 0;
    }

    /**
     * @param SkuProducts $productSku
     * @return int
     */
    public function getItemSizeQuantity(SkuProducts $productSku, $sizeId)
    {
        $item = Arr::get($this->items, $productSku->getId());
        return $item ? $item->getSize($sizeId) : 0;
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
    public function getStandardCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getQuantity();
        }, $this->getStandardItems()));
    }

    /**
     * @return int
     */
    public function getPreOrderCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getQuantity();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return int
     */
    public function getStandardPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPrice();
        }, $this->getStandardItems()));
    }

    /**
     * @return int
     */
    public function getPreOrderPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPrice();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return int
     */
    public function getTotalPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPrice();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getDiscountedTotalPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getDiscountedPrice();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getPackagesCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPackagesQuantity();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getSingleItemsCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getSingleItemsQuantity();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getStandardPackagesCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPackagesQuantity();
        }, $this->getStandardItems()));
    }

    /**
     * @return int
     */
    public function getStandardSingleItemsCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getSingleItemsQuantity();
        }, $this->getStandardItems()));
    }

    /**
     * @return int
     */
    public function getPreOrderPackagesCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPackagesQuantity();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return int
     */
    public function getPreOrderSingleItemsCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getSingleItemsQuantity();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return void
     */
    public function saveInSession()
    {
        $this->session->set('cart_items', $this->toArray());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->items as $item) {
            if ($item->getQuantity() > 0) {
                $array[$item->getSkuProduct()->getId()] = [
                    'id' => $item->getSkuProduct()->getId(),
                    'sizes' => $item->getSizes()
                ];
            } else {
                unset($this->items[$item->getSkuProduct()->getId()]);
            }
        }
        return $array;
    }

    /**
     * @return array
     */
    public function toArrayWithExtraInfo()
    {
        $array = [];
        foreach ($this->items as $item) {
            $array[$item->getSkuProduct()->getId()] = [
                'id' => $item->getSkuProduct()->getId(),
                'sizes' => $item->getSizes(),
                'packagesAmount' => $item->getPackagesQuantity()
            ];
        }
        return $array;
    }

    /**
     * @param $user
     * @param $data
     * @return $this
     * @throws \Doctrine\ORM\ORMException
     * @throws CartEmptyException
     */
    public function flushCart($user, $data)
    {
        if (empty($this->items)) {
            throw new CartEmptyException;
        }

        $order = new Orders();

        if ($data['delivery_type'] == 'np') {
            // Nova poshta
            $prefix = 'np_';
        } else {
            // Delivery
            $prefix = 'del_';
        }
        $cities = Arr::get($data, $prefix . 'delivery_city', null);
        $stores = Arr::get($data, $prefix . 'delivery_store', null);

        $order->setCities($cities);
        $order->setStores($stores);

        foreach ($this->items as $item) {
            $cart = new \AppBundle\Entity\Cart();
            $cart->setOrders($order);
            $cart->setSkuProducts($item->getSkuProduct());
            foreach ($item->getSizes() as $sizeId => $sizeCount) {
                $cartProductSize = new CartProductSize();
                $cartProductSize->setCart($cart);
                $cartProductSize->setSize($this->em->getReference('AppBundle:ProductModelSizes', $sizeId));
                $cartProductSize->setQuantity($sizeCount);
                $cart->addSize($cartProductSize);
            }
            $order->addCart($cart);
            $this->em->persist($cart);
        }
        $order->setUsers($user);
        $order->setPhone(Arr::get($data, 'phone'));
        $order->setCarriers($cities->getCarriers());
        $order->setComment(Arr::get($data, 'comment'));
        $order->setPay(Arr::get($data, 'pay'));
        $order->setFio(Arr::get($data, 'name') . ' ' . Arr::get($data, 'surname'));
        $order->setTotalPrice($this->getTotalPrice());
        $order->setType(Orders::TYPE_NORMAL);
        $this->em->persist($order);
        $this->em->flush();

        $this->clear();

        $this->container->get('event_dispatcher')->dispatch('order.created', new OrderEvent($order));

        return $order;
    }

    /**
     * @param UsersEntity $user
     * @return array
     */
    public function getUserOrders(UsersEntity $user)
    {
        return $this->em->getRepository('AppBundle:Orders')
            ->createQueryBuilder('orders')
            ->select('orders')
            ->leftJoin('orders.cart', 'cart')->addSelect('cart')
            ->leftJoin('cart.sizes', 'sizes')->addSelect('sizes')
            ->where('orders.users = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return void
     */
    protected function initCartFromSession()
    {
        $sessionArray = $this->session->get('cart_items', []);
        $skuProducts = $this->em->getRepository('AppBundle:SkuProducts')->findWithModels(array_keys($sessionArray));
        foreach ($skuProducts as $skuProduct) {
            $cartItem = new CartItem($skuProduct, $this->container->get('prices_calculator'));
            $cartItem->setSizes($sessionArray[$skuProduct->getId()]['sizes']);
            $this->items[$skuProduct->getId()] = $cartItem;
        }
        $this->session->set('cart_items', $sessionArray);
    }

}
