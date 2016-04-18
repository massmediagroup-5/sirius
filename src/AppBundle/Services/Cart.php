<?php

namespace AppBundle\Services;

use AppBundle\Entity\CartProductSize;
use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
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
     * @param ProductModelSpecificSize $size
     * @param $quantity
     */
    public function addItemToCard(ProductModelSpecificSize $size, $quantity)
    {
        if (isset($this->items[$size->getModel()->getId()])) {
            // Each size quantity
            $this->items[$size->getModel()->getId()]->addSize($size, $quantity);
        } else {
            $this->items[$size->getModel()->getId()] = new CartItem($size->getModel(),
                $this->container->get('prices_calculator'));
            $this->items[$size->getModel()->getId()]->addSize($size, $quantity);
        }
        $this->saveInSession();
    }

    /**
     * @param ProductModels $model
     * @return $this
     */
    public function removeItem(ProductModels $model)
    {
        unset($this->items[$model->getId()]);
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
     * @param $size
     * @return $this
     */
    public function removeItemSize(ProductModelSpecificSize $size)
    {
        if (isset($this->items[$size->getModel()->getId()])) {
            $this->items[$size->getModel()->getId()]->removeSize($size);
            if (!$this->items[$size->getModel()->getId()]->getQuantity()) {
                $this->removeItem($size->getModel());
            }
            $this->saveInSession();
        }
        return $this;
    }

    /**
     * @param ProductModelSpecificSize $oldSize
     * @param ProductModelSpecificSize $newSize
     * @return $this
     */
    public function changeItemSize(ProductModelSpecificSize $oldSize, ProductModelSpecificSize $newSize)
    {
        $this->items[$oldSize->getModel()->getId()]->changeSize($oldSize, $newSize);
        $this->saveInSession();
        return $this;
    }

    /**
     * @param $size
     * @param $quantity
     * @return $this
     */
    public function changeItemSizeCount(ProductModelSpecificSize $size, $quantity)
    {
        $this->items[$size->getModel()->getId()]->setSize($size, $quantity);
        $this->saveInSession();
        return $this;
    }

    /**
     * @param ProductModels $model
     * @return bool
     */
    public function inCart(ProductModels $model)
    {
        return isset($this->items[$model->getId()]);
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
            return $item->getProductModel();
        }, $this->items);
    }

    /**
     * @param ProductModels $model
     * @return CartItem[]
     */
    public function getItem(ProductModels $model)
    {
        return Arr::get($this->items, $model->getId());
    }

    /**
     * @param ProductModels $model
     * @return int
     */
    public function getItemQuantity(ProductModels $model)
    {
        return isset($this->items[$model->getId()]) ? $this->items[$model->getId()]->getQuantity() : 0;
    }

    /**
     * @param ProductModels $model
     * @return int
     */
    public function getItemPackagesQuantity(ProductModels $model)
    {
        return isset($this->items[$model->getId()]) ? $this->items[$model->getId()]->getPackagesQuantity() : 0;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function getItemSizeQuantity(ProductModelSpecificSize $size)
    {
        $item = Arr::get($this->items, $size->getModel()->getId());
        return $item ? $item->getSize($size)->getQuantity() : 0;
    }

    /**
     * @return CartItem[]
     */
    public function getPreOrderItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return $item->getProductModel()->hasPreOrderSize();
        });
    }

    /**
     * @return CartItem[]
     */
    public function getStandardItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return !$item->getProductModel()->hasPreOrderSize();
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
        return array_sum(array_map(function (CartItem $item) {
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
                $array[$item->getProductModel()->getId()] = [
                    'id' => $item->getProductModel()->getId(),
                ];
                $sizes = [];
                foreach ($item->getSizes() as $size) {
                    $sizes[$size->getSize()->getId()] = [
                        'id' => $size->getSize()->getId(),
                        'quantity' => $size->getQuantity()
                    ];
                }
                $array[$item->getProductModel()->getId()]['sizes'] = $sizes;
            } else {
                unset($this->items[$item->getProductModel()->getId()]);
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
            $array[$item->getProductModel()->getId()] = [
                'id' => $item->getProductModel()->getId(),
                'packagesAmount' => $item->getPackagesQuantity()
            ];
            $sizes = [];
            foreach ($item->getSizes() as $size) {
                $sizes[$size->getSize()->getId()] = [
                    'id' => $size->getSize()->getId(),
                    'quantity' => $size->getQuantity()
                ];
            }
            $array[$item->getProductModel()->getId()]['sizes'] = $sizes;
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
            foreach ($item->getSizes() as $sizeId => $size) {
                $orderSize = new OrderProductSize();
                $orderSize->setOrder($order);
                $orderSize->setQuantity($size->getQuantity());
                $orderSize->setDiscountedTotalPrice($size->getDiscountedPrice());
                $orderSize->setTotalPrice($size->getPrice());
                $orderSize->setSize($size);
                $order->addSize($orderSize);
            }
            $this->em->persist($order);
        }
        $order->setUsers($user);
        $order->setPhone(Arr::get($data, 'phone'));
        $order->setCarriers($cities->getCarriers());
        $order->setComment(Arr::get($data, 'comment'));
        $order->setPay(Arr::get($data, 'pay'));
        $order->setFio(Arr::get($data, 'name') . ' ' . Arr::get($data, 'surname'));
        $order->setTotalPrice($this->getTotalPrice());
        $order->setDiscountedTotalPrice($this->getDiscountedTotalPrice());
        $order->setType(Orders::TYPE_NORMAL);
        $order->setStatus($this->em->getRepository('AppBundle:OrderStatus')->findOneBy(['code' => 'new']));
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
        $ids = [];
        foreach ($sessionArray as $item) {
            foreach ($item['sizes'] as $size) {
                $ids[] = $size['id'];
            }
        }
        $sizes = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->findWithModels($ids);
        foreach ($sizes as $size) {
            $this->addItemToCard($size, $sessionArray[$size->getModel()->getId()]['sizes'][$size->getId()]['quantity']);
        }
        $this->session->set('cart_items', $sessionArray);
    }

}
