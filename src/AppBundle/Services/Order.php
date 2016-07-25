<?php

namespace AppBundle\Services;

use AppBundle\Entity\History;
use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\OrderStatusPay;
use AppBundle\Entity\Unisender;
use AppBundle\Entity\Users as UsersEntity;
use AppBundle\Exception\CartEmptyException;
use AppBundle\Exception\UserInGrayListException;
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
        $this->translator = $this->container->get('translator');
    }

    /**
     * @param $data
     * @param $user
     * @param bool|false $quickFlag
     * @return Orders
     * @throws CartEmptyException
     * @throws UserInGrayListException
     */
    public function orderFromCart($data, $user, $quickFlag = false)
    {
        if (($user) && ($user->getGrayListFlag())) {
            throw new UserInGrayListException;
        }

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

            $preOrder->setPreOrderFlag(true);

            if ($order) {
                $order->setRelatedOrder($preOrder);
                $preOrder->setRelatedOrder($order);
            } else {
                $order = $preOrder;
            }

            $this->em->persist($preOrder);
        }

        if ($user) {
            $user->decrementBonuses(Arr::get($data, 'bonuses', 0));

            $this->em->persist($user);
        }

        $this->em->persist($order);

        $this->em->flush();

        $cart->clear();

        return $order;
    }

    /**
     * @param $order
     * @param $size
     * @param bool|false $quantity
     * @param bool|false $addHistory
     * @return Orders
     * @throws CartEmptyException
     */
    public function moveSize(Orders $order, OrderProductSize $size, $quantity = false, $addHistory = true)
    {
        $order->getSizes();
        $relatedOrder = $order->getRelatedOrder();
        if ($size->getQuantity() > $quantity) {
            $size->setQuantity($size->getQuantity() - $quantity);
            $this->em->persist($size);
        } else {
            $order->removeSize($size);
        }

        // Add history to order
        if ($addHistory) {
            $type = $order->getPreOrderFlag() ? OrderHistory::TYPE_MOVE_TO_ORDER : OrderHistory::TYPE_MOVE_TO_PRE_ORDER;
            $this->addOrderHistoryItem($order, $type, 'sizes', $size->getSize(), null, $this->getUser(), [
                'quantity' => $quantity
            ]);
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
            if ($relatedSize->getSize()->getId() == $size->getSize()->getId()) {
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
            $order->setRelatedOrder(null);

            $type = $relatedOrder->getPreOrderFlag() ? OrderHistory::TYPE_MERGED_WITH_PRE_ORDER : OrderHistory::TYPE_MERGED_WITH_ORDER;
            $this->addOrderHistoryItem($relatedOrder, $type, null, null, null, $this->getUser());

            $this->em->persist($relatedOrder);

            $this->em->remove($order);

            $this->em->flush();
            return $relatedOrder;
        }

        $order->setPreOrderFlag(!$order->getPreOrderFlag());

        $type = $relatedOrder->getPreOrderFlag() ? OrderHistory::TYPE_ORDER_TO_PRE_ORDER : OrderHistory::TYPE_PRE_ORDER_TO_ORDER;
        $this->addOrderHistoryItem($order, $type, null, null, null, $this->getUser());

        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * @param $order
     * @param $sizes
     * @param $addHistory
     * @return Orders
     * @throws CartEmptyException
     */
    public function addSizes(Orders $order, array $sizes, $addHistory = true)
    {
        $availableSizes = $order->getSizes();

        foreach ($sizes as $size) {
            list($size, $quantity) = $size;

            foreach ($availableSizes as $key => $availableSize) {
                if ($availableSize->getSize()->getId() == $size->getId()) {
                    $beforeQuantity = $availableSize->getQuantity();

                    $availableSize->incrementQuantity($quantity);
                    $availableSizes->set($key, $availableSize);

                    $afterQuantity = $availableSize->getQuantity();

                    if ($addHistory) {
                        $this->addOrderHistoryItem($order, OrderHistory::TYPE_RELATION_CHANGE, 'sizes', $beforeQuantity,
                            $afterQuantity, $this->getUser(), ['id' => $size->getId(), 'change' => 'quantity']);
                    }
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

            $this->em->persist($orderSize);
            $this->em->flush();

            if ($addHistory) {
                $this->addOrderHistoryItem($order, OrderHistory::TYPE_RELATION_ADD, 'sizes', null, $orderSize->getId(),
                    $this->getUser());
            }
        }
        $order->setSizes($availableSizes);

        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * @param $order
     * @param $size
     * @param $addHistory
     * @return Orders
     * @throws CartEmptyException
     */
    public function removeSize(Orders $order, $size, $addHistory = true)
    {
        $this->em->remove($size);

        if ($addHistory) {
            $this->addOrderHistoryItem($order, OrderHistory::TYPE_RELATION_REMOVE, 'sizes', null, null,
                $this->getUser(), ['size' => clone $size]);
        }

        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * @param Orders $order
     * @return Orders
     */
    public function sendOrderEmail(Orders $order)
    {
        $body = $this->container->get('twig')->render('AppBundle:mails/order.html.twig', [
                'order' => $order
            ]
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('Order from orders@sirius.com.ua')
            ->setFrom('orders@sirius-sport.com')
            ->setTo($this->container->get('options')->getParamValue('email'))
            ->setBody($body)
            ->setContentType("text/html");
        $this->container->get('mailer')->send($message);

        return $order;
    }

    /**
     * @param Orders $order
     */
    public function sendStatusInfo(Orders $order)
    {
        $allowedFields = [
            'payStatus',
            'status'
        ];
        $orderChanges = $this->em->getUnitOfWork()->getEntityChangeSet($order);
        foreach ($orderChanges as $fieldName => $orderChange) {
            if (in_array($fieldName, $allowedFields)) {
                switch ($fieldName) {
                    case "status":
                        $orderStatus = $order->getStatus();
                        break;
                    case "payStatus":
                        $orderStatus = $order->getPayStatus();
                        break;
                }
                $uniSender = $this->em->getRepository('AppBundle:Unisender')->findOneBy(['active' => '1']);
                if ($orderStatus) {
                    if ($uniSender) {
                        if (($orderStatus->getSendClient()) && (!empty($orderStatus->getSendClientText()))) {
                            $client_sms_status = $this->sendSmsRequest(
                                $uniSender,
                                $order->getPhone(),
                                $order->getId(),
                                $orderStatus->getSendClientText()
                            );
                            if ($client_sms_status['error'] == false) {
                                // если без ошибок то сохраняем идентификатор смс
                                $order->setClientSmsId($client_sms_status['sms_id']);
                            } else {
                                // если ошибка то сохраняем текст ошибки
                                $order->setClientSmsStatus($client_sms_status['error']);
                            }
                        }
                        if (($orderStatus->getSendManager()) && (!empty($orderStatus->getSendManagerText()))) {
                            $phones = explode(',', $uniSender->getPhones());
                            foreach ($phones as $phone) {
                                $this->sendSmsRequest(
                                    $uniSender,
                                    $phone,
                                    $order->getId() ? $order->getId() : date('G:i:s d-m-Y', time()),
                                    $orderStatus->getSendManagerText()
                                );
                            }
                        }
                    }
                    if (($orderStatus->getSendClientEmail()) && (!empty($orderStatus->getSendClientEmailText())) && ($order->getUsers())) {
                        $body = sprintf(
                            $orderStatus->getSendClientEmailText(),
                            $orderStatus->getId() // %s
                        );
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Order from orders@sirius-sport.com')
                            ->setFrom('orders@sirius-sport.com')
                            ->addTo($order->getUsers()->getEmail())
                            ->setBody($body)
                            ->setContentType("text/html");
                        $this->container->get('mailer')->send($message);
                    }
                }
            }
        }

        $this->recomputeChanges($order);
    }

    /**
     * sendSmsRequest
     *
     * @param Unisender $uniSender
     * @param mixed $phone
     * @param string $dynamic_text
     * @param string $sms_text
     *
     * return mixed
     */
    public function sendSmsRequest($uniSender, $phone, $dynamic_text, $sms_text = null)
    {
        if ($curl = curl_init()) {
            $sms_body = sprintf(
                $sms_text,
                $dynamic_text // %s
            );
            // массив передаваемых параметров
            $parameters_array = array(
                'api_key' => $uniSender->getApiKey(),
                'phone' => preg_replace("/[^0-9]/", '', strip_tags($phone)),
                'sender' => $uniSender->getSenderName(),
                'text' => $sms_body
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters_array);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_URL, 'http://api.unisender.com/ru/api/sendSms?format=json');
            $response = curl_exec($curl);

            if ($response) {
                // Раскодируем ответ API-сервера
                $jsonObj = json_decode($response);
                if (null === $jsonObj) {
                    // Ошибка в полученном ответе
                    $result['error'] = "Invalid JSON";
                } elseif (!empty($jsonObj->result->error)) {
                    // Ошибка отправки сообщения
                    $result['error'] = "An error occured: " . $jsonObj->result->error . "(code: " . $jsonObj->result->code . ")";
                } else {
                    // Сообщение успешно отправлено
                    $result['sms_id'] = $jsonObj->result->sms_id;
                    $result['error'] = false;
                }
            } else {
                // Ошибка соединения с API-сервером
                $result['error'] = "API access error";
            }
            curl_close($curl);
            return $result;
        }
    }

    /**
     * @param Orders $order
     */
    public function processOrderChanges(Orders $order)
    {
        if ($order->isIgnoreFlushEvent()) {
            return;
        }

        $allowedFields = [
            'fio',
            'phone',
            'email',
            'payStatus',
            'status',
            'cities',
            'stores',
            'individualDiscount',
            'additionalSolar'
        ];
        $relationsNames = $this->em->getClassMetadata('AppBundle:Orders')->getAssociationNames();

        $uow = $this->em->getUnitOfWork();
        if ($order->getId() === null) {
            $this->addOrderHistoryItem($order, OrderHistory::TYPE_CREATED);
        } else {
            $orderChanges = $uow->getEntityChangeSet($order);
            foreach ($orderChanges as $fieldName => $orderChange) {
                if (in_array($fieldName, $allowedFields)) {
                    if (in_array($fieldName, $relationsNames)) {
                        $this->addOrderHistoryItem($order, OrderHistory::TYPE_CHANGE, $fieldName,
                            $orderChange[0]->getId(), $orderChange[1]->getId(), $this->getUser());
                    } else {
                        $this->addOrderHistoryItem($order, OrderHistory::TYPE_CHANGE, $fieldName, $orderChange[0],
                            $orderChange[1], $this->getUser());
                    }
                }
                if ($fieldName == 'payStatus' && $orderChange[1]) {
                    $doneDate = $orderChange[1]->getCode() == OrderStatusPay::CODE_PAID ? new \DateTime() : null;
                    $order->setDoneTime($doneDate);
                }
            }
        }

        $this->recomputeChanges($order);
    }

    /**
     * @param Orders $order
     * @param OrderHistory $historyItem
     * @return bool
     */
    public function cancelOrderHistory(Orders $order, OrderHistory $historyItem)
    {
        $changeType = $historyItem->getChangeType();
        $relationsNames = $this->em->getClassMetadata('AppBundle:Orders')->getAssociationNames();
        $canRemoveFlag = true;

        if ($changeType == OrderHistory::TYPE_CHANGE) {
            $setter = 'set' . lcfirst($historyItem->getChanged());
            if (in_array($historyItem->getChanged(), $relationsNames)) {
                if ($historyItem->getChanged() == 'status') {
                    $order->$setter($this->em->getRepository('AppBundle:OrderStatus')->find($historyItem->getFrom()));
                } elseif ($historyItem->getChanged() == 'payStatus') {
                    $order->$setter($this->em->getRepository('AppBundle:OrderStatusPay')->find($historyItem->getFrom()));
                }
            } else {
                $order->$setter($historyItem->getFrom());
            }
            $canRemoveFlag = true;

        } elseif ($changeType == OrderHistory::TYPE_RELATION_CHANGE) {
            $changedField = $historyItem->getAdditional('change');
            if ($historyItem->getChanged() == 'sizes' && $changedField == 'quantity') {
                $size = $this->em->getRepository('AppBundle:OrderProductSize')->find($historyItem->getAdditional('id'));
                $size->setQuantity($historyItem->getFrom());
                $this->em->persist($size);
                $canRemoveFlag = true;
            }

        } elseif ($changeType == OrderHistory::TYPE_RELATION_REMOVE) {
            if ($historyItem->getChanged() == 'sizes') {
                $orderSize = $historyItem->getAdditional('size');
                $size = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->find($orderSize->getSize()->getId());


                $this->addSizes($order, [[$size, $orderSize->getQuantity()]], false);
                $canRemoveFlag = true;
            }

        } elseif ($changeType == OrderHistory::TYPE_RELATION_ADD) {
            if ($historyItem->getChanged() == 'sizes') {
                $size = $this->em->getRepository('AppBundle:OrderProductSize')->find($historyItem->getTo());
                $this->removeSize($order, $size, false);
                $canRemoveFlag = true;
            }

        } elseif ($changeType == OrderHistory::TYPE_MOVE_TO_ORDER || $changeType == OrderHistory::TYPE_MOVE_TO_PRE_ORDER) {
            $changedField = $historyItem->getAdditional('change');
            if ($historyItem->getChanged() == 'sizes' && $changedField == 'quantity') {
                $size = $this->em->getRepository('AppBundle:OrderProductSize')->find($historyItem->getAdditional('id'));
                $this->moveSize($order->getRelatedOrder(), $size, $historyItem->getAdditional('quantity'), false);
                $canRemoveFlag = true;
                $this->em->persist($size);
            }

        } elseif ($changeType == OrderHistory::TYPE_MERGED_WITH_PRE_ORDER) {
            // can`t undo changes

        } elseif ($changeType == OrderHistory::TYPE_MERGED_WITH_ORDER) {
            // can`t undo changes

        } elseif ($changeType == OrderHistory::TYPE_PRE_ORDER_TO_ORDER) {
            // can`t undo changes, this change can be undo by "change flag" button

        } elseif ($changeType == OrderHistory::TYPE_ORDER_TO_PRE_ORDER) {
            // can`t undo changes, this change can be undo by "change flag" button

        }

        if ($canRemoveFlag) {
            $order->setIgnoreFlushEvent(true);
            $this->em->remove($historyItem);
            $this->em->persist($order);
            $this->em->flush();
            return true;
        }

        return false;
    }

    /**
     * @param Orders $order
     * @return Orders
     */
    public function cancelOrder(Orders $order)
    {
        $cancelStatus = $this->em->getRepository('AppBundle:OrderStatus')
            ->findOneBy(['code' => 'canceled']);

        $order->setStatus($cancelStatus);

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * Append bonuses to orders
     */
    public function appendBonusesToOrders()
    {
        $interval = $this->container->getParameter('orders.add_bonuses_days_interval');

        $orders = $this->em->getRepository('AppBundle:Orders')->findToAppendBonuses($interval);

        foreach ($orders as $order) {
            $this->appendBonuses($order);
        }
    }

    /**
     * @param Orders $order
     */
    protected function appendBonuses(Orders $order)
    {
        if ($user = $order->getUsers()) {
            $sum = $order->getIndividualDiscountedTotalPrice();
            $user->incrementBonuses($this->container->get('prices_calculator')->getBonusesToSum($sum));

            $order->setBonusesEnrolled(true);

            $this->em->persist($order);
            $this->em->persist($user);
            $this->em->flush($user);
        }
    }

    /**
     * @param Orders $order
     * @param $type
     * @param $changed
     * @param $from
     * @param $to
     * @param $user
     * @param array $additional
     */
    protected function addOrderHistoryItem(
        Orders $order,
        $type,
        $changed = null,
        $from = null,
        $to = null,
        $user = null,
        $additional = []
    ) {
        $historyItem = new OrderHistory();
        $historyItem->setChanged($changed);
        $historyItem->setChangeType($type);
        $historyItem->setFrom($from);
        $historyItem->setTo($to);
        $historyItem->setUser($user);
        $historyItem->setOrder($order);
        $historyItem->setAdditional($additional);
        $order->addHistory($historyItem);
    }

    /**
     * @return mixed
     */
    protected function getUser()
    {
        return $this->container->get('security.token_storage')->getToken()->getUser();
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
            $bonuses = Arr::get($data, 'bonuses', 0);

            $order->setCities($cities);
            $order->setStores($stores);
            $order->setCarriers($cities->getCarriers());
            $order->setComment(Arr::get($data, 'comment'));
            $order->setBonuses($bonuses);
            $order->setPay(Arr::get($data, 'pay'));
            $order->setFio(Arr::get($data, 'name') . ' ' . Arr::get($data, 'surname'));
            $order->setType(Orders::TYPE_NORMAL);
        } else {
            // todo temporary, 1 - Nova poshta
            $order->setCarriers($this->em->getRepository('AppBundle:Carriers')->findOneById(1));
            $order->setType(Orders::TYPE_QUICK);
        }

        $order->setStatus($this->em->getRepository('AppBundle:OrderStatus')->findOneBy(['code' => 'new']));
        $order->setUsers($user ?: null);
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
            ->orderBy('orders.id', 'DESC')
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

    /**
     * @param $entity
     */
    protected function recomputeChanges($entity)
    {
        // Recompute order changes
        $this->em->getUnitOfWork()->recomputeSingleEntityChangeSet($this->em->getClassMetadata(get_class($entity)),
            $entity);
        $this->em->persist($entity);
    }
}
