<?php

namespace AppBundle\Services;

use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Users as UsersEntity;
use AppBundle\Event\OrderEvent;
use AppBundle\Exception\CartEmptyException;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class Order
 * @package AppBundle\Services
 * @author R. Slobodzian
 */
class Order
{

    /**
     * em
     *
     * @var EntityManager
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param $data
     * @param $user
     * @param bool|false $quickFlag
     * @return Orders
     * @throws CartEmptyException
     */
    public function orderFromCart($data, $user, $quickFlag = false)
    {
        $cart = $this->container->get('cart');

        if (!$cart->getItems()) {
            throw new CartEmptyException;
        }

        $order = null;
        if ($standardSizes = $cart->getStandardSizes()) {
            $order = $this->createOrder($standardSizes, $data, $user, $quickFlag);
        }

        if ($preOrderSizes = $cart->getPreOrderSizes()) {
            $preOrder = $this->createOrder($preOrderSizes, $data, $user, $quickFlag);

            $order->setPreOrderFlag(true);

            if ($order) {
                $order->setRelatedOrder($preOrder);
                $preOrder->setRelatedOrder($order);
            } else {
                $order = $preOrder;
            }

            $this->em->persist($preOrder);
        }
        $this->em->persist($order);

        $this->em->flush();

        $cart->clear();

        $this->container->get('event_dispatcher')->dispatch('order.created', new OrderEvent($order));

        return $order;
    }

    /**
     * @param $order
     * @param $size
     * @param bool|false $quantity
     * @return Orders
     * @throws CartEmptyException
     */
    public function moveSize(Orders $order, OrderProductSize $size, $quantity = false)
    {
        $order->getSizes();
        $relatedOrder = $order->getRelatedOrder();
        if ($size->getQuantity() > $quantity) {
            $size->setQuantity($size->getQuantity() - $quantity);
            $this->em->persist($size);
        } else {
            $order->removeSize($size);
        }

        if (!$relatedOrder) {
            $relatedOrder = clone $order;
            $relatedOrder->setRelatedOrder($order);
            $order->setRelatedOrder($relatedOrder);
            $relatedOrder->setPreOrderFlag(!$relatedOrder->getPreOrderFlag());
        }
        $relatedSizes = $relatedOrder->getSizes();

        $foundedFlag = false;
        // Update size or add new
        foreach ($relatedSizes as $key => $relatedSize) {
            if ($relatedSize->getId() == $size->getId()) {
                $relatedSize->incrementQuantity($quantity);
                $relatedSizes->set($key, $relatedSize);
                $foundedFlag = true;
                break;
            }
        }
        if (!$foundedFlag) {
            $clonedSize = clone $size;
            $clonedSize->setOrder($relatedOrder);
            $clonedSize->setQuantity($quantity);
            $relatedSizes->add($clonedSize);
        }

        $relatedOrder->setSizes($relatedSizes);
        $this->em->persist($order);
        $this->em->persist($relatedOrder);

        $this->em->flush();

        return $order;
    }

    /**
     * @param Orders $order
     * @return Orders
     */
    public function changePreOrderFlag(Orders $order)
    {
        if ($relatedOrder = $order->getRelatedOrder()) {
            $this->mergeSizesToOrder($relatedOrder, $order->getSizes());
            $relatedOrder->setRelatedOrder(null);
            $this->em->persist($relatedOrder);
            $this->em->remove($order);

            $this->em->flush();
            return $relatedOrder;
        }

        $order->setPreOrderFlag(!$order->getPreOrderFlag());
        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * @param $order
     * @param $sizes
     * @return Orders
     * @throws CartEmptyException
     */
    public function addSizes(Orders $order, array $sizes)
    {
        $availableSizes = $order->getSizes();

        foreach ($sizes as $size) {
            list($size, $quantity) = $size;

            foreach ($availableSizes as $key => $availableSize) {
                if ($availableSize->getSize()->getId() == $size->getId()) {
                    $availableSize->incrementQuantity($quantity);
                    $availableSizes->set($key, $availableSize);
                    continue 2;
                }
            }
            $orderSize = new OrderProductSize();
            $orderSize->setSize($size);
            $orderSize->setQuantity($quantity);
            $orderSize->setOrder($order);
            $orderSize->setDiscountedTotalPricePerItem($this->container->get('prices_calculator')->getDiscountedPrice($size));
            $orderSize->setTotalPricePerItem($this->container->get('prices_calculator')->getPrice($size));
            $availableSizes->add($orderSize);
        }
        $order->setSizes($availableSizes);

        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * @param \AppBundle\Model\CartSize[] $sizes
     * @param $data
     * @param $user
     * @param bool|false $quickFlag
     * @return Orders
     * @throws CartEmptyException
     */
    protected function createOrder($sizes, $data, $user, $quickFlag = false)
    {
        $order = new Orders();

        if (!$quickFlag) {
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
            $order->setCarriers($cities->getCarriers());
            $order->setComment(Arr::get($data, 'comment'));
            $order->setPay(Arr::get($data, 'pay'));
            $order->setFio(Arr::get($data, 'name') . ' ' . Arr::get($data, 'surname'));
            $order->setType(Orders::TYPE_NORMAL);
        }

        $order->setStatus($this->em->getRepository('AppBundle:OrderStatus')->findOneBy(['code' => 'new']));
        $order->setUsers($user ?: null);
        $order->setQuickFlag($quickFlag);
        $order->setPhone(Arr::get($data, 'phone'));

        foreach ($sizes as $size) {
            $orderSize = new OrderProductSize();
            $orderSize->setOrder($order);
            $orderSize->setQuantity($size->getQuantity());
            $orderSize->setDiscountedTotalPricePerItem($size->getDiscountedPricePerItem());
            $orderSize->setTotalPricePerItem($size->getPricePerItem());
            $orderSize->setSize($size->getSize());
            $order->addSize($orderSize);
        }

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
            ->leftJoin('orders.sizes', 'orderSizes')->addSelect('orderSizes')
            ->where('orders.users = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Orders $order
     * @param $sizes
     */
    protected function mergeSizesToOrder(Orders $order, $sizes)
    {
        $availableSizes = $order->getSizes();

        foreach ($sizes as $size) {
            foreach ($availableSizes as $key => $availableSize) {
                if ($availableSize->getSize()->getId() == $size->getSize()->getId()) {
                    $availableSize->incrementQuantity($size->getQuantity());
                    $availableSizes->set($key, $availableSize);
                    continue 2;
                }
            }
            $size->setOrder($order);
            $availableSizes->add($size);
        }
        $order->setSizes($availableSizes);
    }
}
