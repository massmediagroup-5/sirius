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
        if($standardSizes = $cart->getStandardSizes()) {
            $order = $this->createOrder($standardSizes, $data, $user, $quickFlag);
        }

        if($preOrderSizes = $cart->getPreOrderSizes()) {
            $preOrder = $this->createOrder($preOrderSizes, $data, $user, $quickFlag);

            if($order) {
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
     * @param \AppBundle\Model\CartSize[] $sizes
     * @param $price
     * @param $data
     * @param $user
     * @param bool|false $quickFlag
     * @return Orders
     * @throws CartEmptyException
     */
    protected function createOrder($sizes, $data, $user, $quickFlag = false) {
        $order = new Orders();

        if(!$quickFlag) {
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
            $orderSize->setDiscountedTotalPrice($size->getDiscountedPrice());
            $orderSize->setTotalPrice($size->getPrice());
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
}
