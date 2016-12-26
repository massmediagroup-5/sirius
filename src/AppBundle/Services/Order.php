<?php

namespace AppBundle\Services;

use AppBundle\Entity\Carriers;
use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\OrderSmsInfo;
use AppBundle\Entity\OrderStatusPay;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\Unisender;
use AppBundle\Entity\Users as UsersEntity;
use AppBundle\Event\CancelOrderEvent;
use AppBundle\Event\OrderEvent;
use AppBundle\Exception\CartEmptyException;
use AppBundle\Exception\BuyerAccessDeniedException;
use AppBundle\Exception\ImpossibleMoveToPreOrder;
use AppBundle\Exception\ImpossibleToAddSizeToOrder;
use AppBundle\HistoryItem\HistoryCreatedItem;
use AppBundle\HistoryItem\OrderHistoryChangedItem;
use AppBundle\HistoryItem\OrderHistoryCreatedItem;
use AppBundle\HistoryItem\OrderHistoryMergedWithRelatedItem;
use AppBundle\HistoryItem\OrderHistoryMoveFromSizeItem;
use AppBundle\HistoryItem\OrderHistoryMoveToSizeItem;
use AppBundle\HistoryItem\OrderHistoryRelationAddedItem;
use AppBundle\HistoryItem\OrderHistoryRelationChangedItem;
use AppBundle\HistoryItem\OrderHistoryRelationRemovedItem;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class Order
 * @package AppBundle\Services
 * @author R. Slobodzian
 */
class Order
{
    const HISTORY_PREFIX = 'order';

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
     * @var HistoryManager
     */
    protected $historyManager;

    /**
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $em;
        $this->translator = $this->container->get('translator');
        $this->historyManager = $this->container->get('history_manager');
    }

    /**
     * @param $data
     * @param $user
     * @param bool|false $quickFlag
     *
     * @return Orders
     * @throws CartEmptyException
     * @throws BuyerAccessDeniedException
     */
    public function orderFromCart($data, UsersEntity $user = null, $quickFlag = false)
    {
        if (($user) && ($this->container->get('security.authorization_checker')->isGranted('ROLE_BLACK_LIST'))) {
            throw new BuyerAccessDeniedException;
        }

        $cart = $this->container->get('cart');

        if (!$cart->getItems()) {
            throw new CartEmptyException;
        }

        $this->em->getConnection()->beginTransaction();

        $order = null;
        if ($cart->getStandardCount()) {
            $order = $this->createOrder($cart->getSizes(), $data, $user, $quickFlag, false);
        }

        if ($cart->getPreOrderCount()) {
            $preOrder = $this->createOrder($cart->getSizes(), $data, $user, $quickFlag, true);

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

        $order->setLoyalityDiscount($cart->getLoyaltyDiscount());
        $order->setUpSellDiscount($cart->getUpSellShareDiscount());

        $this->em->persist($order);

        $this->em->flush();

        $cart->clear();

        $this->em->getConnection()->commit();

        return $order;
    }

    /**
     * @param $order
     * @param $size
     * @param bool|false $quantity
     *
     * @return Orders
     * @throws CartEmptyException
     */
    public function moveSize(Orders $order, OrderProductSize $size, $quantity = false)
    {
        $order->getSizes();
        $relatedOrder = $order->getRelatedOrder();
        $size->setQuantity($size->getQuantity() - $quantity);
        if ($size->getQuantity() > $quantity) {
            $this->em->persist($size);
        } else {
            $order->removeSize($size);
        }

        // Add history to order
        (new OrderHistoryMoveToSizeItem($this->container))->createHistoryItem($order, $size, $quantity,
            $this->getUser());

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
            $relatedSize = clone $size;
            $relatedSize->setOrder($relatedOrder);
            $relatedSize->setQuantity($quantity);
            $relatedSizes->add($relatedSize);
        }

        $relatedOrder->setSizes($relatedSizes);

        // Add history to order
        (new OrderHistoryMoveFromSizeItem($this->container))->createHistoryItem($relatedOrder, $relatedSize, $quantity,
            $this->getUser());

        $this->em->persist($order);
        $this->em->persist($relatedOrder);

        $this->em->flush();

        return $order;
    }

    /**
     * @param Orders $order
     * @return Orders
     * @throws ImpossibleMoveToPreOrder
     */
    public function changePreOrderFlag(Orders $order)
    {
        // When is not pre order and order contain not pre-ordered sizes - forbid to change flag
        if (!$order->getPreOrderFlag()) {
            foreach ($order->getSizes() as $size) {
                if (!$size->getSize()->getPreOrderFlag()) {
                    throw new ImpossibleMoveToPreOrder;
                }
            }
        }

        if ($relatedOrder = $order->getRelatedOrder()) {
            $this->mergeSizesToOrder($relatedOrder, $order->getSizes());
            $relatedOrder->setRelatedOrder(null);
            $order->setRelatedOrder(null);

            (new OrderHistoryMergedWithRelatedItem($this->container))->createHistoryItem($relatedOrder,
                $this->getUser());

            $this->em->persist($relatedOrder);

            $this->em->remove($order);

            $this->em->flush();

            return $relatedOrder;
        }

        $order->setPreOrderFlag(!$order->getPreOrderFlag());

        (new OrderHistoryMergedWithRelatedItem($this->container))->createHistoryItem($order, $this->getUser());

        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * @param Orders $order
     * @param array $sizes
     * @return Orders
     * @throws ImpossibleToAddSizeToOrder
     */
    public function addSizes(Orders $order, array $sizes)
    {
        foreach ($sizes as $size) {
            if (!$this->canAddSizeToOrder($order, $size[0])) {
                throw new ImpossibleToAddSizeToOrder;
            }
        }

        foreach ($sizes as $size) {
            list($size, $quantity) = $size;
            $this->incrementSizeCount($order, $size, $quantity);
        }

        $this->em->persist($order);

        $this->em->flush();

        return $order;
    }

    /**
     * @param Orders $order
     * @param ProductModelSpecificSize $size
     * @param $quantity
     * @param bool $flushFlag
     * @throws ImpossibleToAddSizeToOrder
     */
    public function incrementSizeCount(Orders $order, ProductModelSpecificSize $size, $quantity, $flushFlag = false)
    {
        $this->changeSizeCount($order, $size, $quantity, true, $flushFlag);
    }

    /**
     * @param Orders $order
     * @param ProductModelSpecificSize $size
     * @param $quantity
     * @param bool $flushFlag
     * @throws ImpossibleToAddSizeToOrder
     */
    public function setSizeCount(Orders $order, ProductModelSpecificSize $size, $quantity, $flushFlag = false)
    {
        $this->changeSizeCount($order, $size, $quantity, false, $flushFlag);
    }

    /**
     * @param Orders $order
     * @param ProductModelSpecificSize $size
     * @param $quantity
     * @param bool $increment
     * @param bool $flushFlag
     */
    public function changeSizeCount(
        Orders $order,
        ProductModelSpecificSize $size,
        $quantity,
        $increment = false,
        $flushFlag = false
    ) {
        $availableSizes = $order->getSizes();
        $foundedFlag = false;

        foreach ($availableSizes as $key => $availableSize) {
            if ($availableSize->getSize()->getId() == $size->getId()) {
                $beforeQuantity = $availableSize->getQuantity();

                if ($increment) {
                    $availableSize->incrementQuantity($quantity);
                } else {
                    $availableSize->setQuantity($quantity);
                }

                $availableSizes->set($key, $availableSize);

                $afterQuantity = $availableSize->getQuantity();

                if ($afterQuantity < 1) {
                    throw new \RuntimeException("Can`t set size quantity lower than 1");
                }

                (new OrderHistoryRelationChangedItem($this->container))->createHistoryItem($order, 'sizes', $size,
                    'quantity', $beforeQuantity, $afterQuantity, $this->getUser());

                $foundedFlag = true;
            }
        }

        if (!$foundedFlag) {
            if ($quantity > 0) {
                $orderSize = new OrderProductSize();
                $orderSize->setSize($size);
                $orderSize->setQuantity($quantity);
                $orderSize->setOrder($order);
                $orderSize->setDiscountedTotalPricePerItem($this->container->get('prices_calculator')->getDiscountedPrice($size));
                $orderSize->setTotalPricePerItem($this->container->get('prices_calculator')->getPrice($size));
                $availableSizes->add($orderSize);

                (new OrderHistoryRelationAddedItem($this->container))->createHistoryItem($order, 'sizes', $orderSize,
                    $this->getUser());
            } else {
                throw new \RuntimeException("Can`t add new size with quantity lower than 1");
            }
        }

        $order->setSizes($availableSizes);

        if ($flushFlag) {
            $this->em->persist($order);
            $this->em->flush();
        }
    }

    /**
     * @param $order
     * @param $size
     * @param $quantity
     *
     * @return Orders
     * @throws CartEmptyException
     */
    public function removeSize(Orders $order, OrderProductSize $size, $quantity)
    {
        if ($quantity === null || $quantity >= $size->getQuantity()) {
            $this->em->remove($size);
        } else {
            $size->setQuantity($size->getQuantity() - $quantity);
            $this->em->persist($size);
        }

        $removedQuantity = $quantity === null ? $size->getQuantity() : $quantity;
        (new OrderHistoryRelationRemovedItem($this->container))->createHistoryItem($order, 'sizes', $size, $removedQuantity,
            $this->getUser());

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @param Orders $order
     *
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
                            $now = time();
                            $hour = date('G', $now);
                            if ($hour >= 20 or $hour <= 8) {
                                $smsText = $orderStatus->getSendClientNightText();
                            } else {
                                $smsText = $orderStatus->getSendClientText();
                            }
                            $client_sms_status = $this->sendSmsRequest(
                                $uniSender,
                                $order->getPhone(),
                                $order->getId() ? $order->getId() : date('G:i:s d-m-Y', time()),
                                $smsText
                            );
                            $OrderSmsInfo = new OrderSmsInfo();
                            $OrderSmsInfo->setOrder($order);
                            $OrderSmsInfo->setType(sprintf($orderStatus->getSendClientText(), ($order->getId() ? $order->getId() : date('G:i:s d-m-Y', time() )) ));
                            if ($client_sms_status['error'] == false) {
                                // если без ошибок то сохраняем идентификатор смс
//                                $order->setClientSmsId($client_sms_status['sms_id']);
                                $OrderSmsInfo->setSmsId($client_sms_status['sms_id']);
                            } else {
                                // если ошибка то сохраняем текст ошибки
//                                $order->setClientSmsStatus($client_sms_status['error']);
                                $OrderSmsInfo->setSmsStatus($client_sms_status['error']);
                            }
                            $this->em->persist($OrderSmsInfo);
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
                            $order->getId() ? $order->getId() : date('G:i:s d-m-Y', time()) // %s
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
     * @return mixed
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
                } elseif (isset($jsonObj->result->error)) {
                    // Ошибка отправки сообщения
                    $result['error'] = "An error occured: {$jsonObj->result->error} (code: {$jsonObj->result->error})";
                } elseif (isset($jsonObj->error)) {
                    // Ошибка отправки сообщения
                    $result['error'] = "An error occured: {$jsonObj->error}";
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
     * @return int count of updated sms statuses
     */
    public function checkSmsStatus()
    {
        $orders = $this->em
            ->createQuery('SELECT o FROM AppBundle\Entity\Orders o WHERE o.clientSmsId != :where AND o.clientSmsStatus NOT LIKE :like')
            ->setParameter('where', 'null')
            ->setParameter('like', '%Статус окончательный%')
            ->getResult();
        $count = 0;
        $status_arr = [
            'not_sent' => 'Сообщение пока не отправлено, ждёт отправки. Статус будет изменён после отправки',
            'ok_sent' => 'Сообщение отправлено, но статус доставки пока неизвестен. Статус временный и может измениться',
            'ok_delivered' => 'Сообщение доставлено. Статус окончательный',
            'err_src_invalid' => 'Доставка невозможна, отправитель задан неправильно. Статус окончательный',
            'err_dest_invalid' => 'Доставка невозможна, указан неправильный номер. Статус окончательный',
            'err_skip_letter' => 'Доставка невозможна, т.к. во время отправки был изменён статус телефона, либо телефон был удалён из списка, либо письмо было удалено. Статус окончательный',
            'err_not_allowed' => 'Доставка невозможна, этот оператор связи не обслуживается. Статус окончательный',
            'err_delivery_failed' => 'Доставка не удалась - обычно по причине указания формально правильного, но несуществующего номера или из-за выключенного телефона. Статус окончательный',
            'err_lost' => 'Сообщение было утеряно, отправитель должен переотправить сообщение самостоятельно, т.к. оригинал не сохранился. Статус окончательный',
            'err_internal' => 'внутренний сбой. Необходима переотправка сообщения. Статус окончательный',
        ];
        foreach ($orders as $order) {
            $result = json_decode($this->sendCheckSmsStatus($order->getClientSmsId()));
            if (!empty($result->error)) {
                $message = "An error occured: " . isset($result->error) ? $result->error : ' ' . "(code: " . isset($result->code) ? $result->code : ' ' . ") " . $status_arr[$result->result->status];
            } else {
                $message = $status_arr[$result->result->status];
            }
            $order->setClientSmsStatus($message);
            $this->em->persist($order);
            $this->em->flush($order);
            $count++;
        }

        return $count;
    }

    /**
     * @param integer|string $sms_id
     *
     * @return bool|mixed
     */
    public function sendCheckSmsStatus($sms_id)
    {
        if ($curl = curl_init()) {
            $uniSender = $this->em->getRepository('AppBundle:Unisender')->findOneBy(['active' => '1']);
            // массив передаваемых параметров
            $parameters_array = array(
                'api_key' => $uniSender->getApiKey(),
                'sms_id' => $sms_id
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters_array);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_URL, 'http://api.unisender.com/ru/api/checkSms?format=json');
            $response = curl_exec($curl);

            return $response;
        } else {
            return false;
        }
    }

    /**
     * @param Orders $order
     */
    public function processOrderChanges(Orders $order)
    {
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

        $uow = $this->em->getUnitOfWork();
        if ($order->getId() === null) {
            (new HistoryCreatedItem($this->container, null, self::HISTORY_PREFIX))->createHistoryItem($order);
        } else {
            $orderChanges = $uow->getEntityChangeSet($order);
            foreach ($orderChanges as $fieldName => $orderChange) {
                if (in_array($fieldName, $allowedFields)) {
                    (new OrderHistoryChangedItem($this->container))->createHistoryItem($order, $fieldName,
                        $orderChange[0], $orderChange[1], $this->getUser());
                }
                if ($fieldName == 'status' && $orderChange[1]) {
                    $doneDate = $orderChange[1]->getCode() == Orders::STATUS_DONE ? new \DateTime() : null;
                    $order->setDoneTime($doneDate);

                    if (in_array($orderChange[1]->getCode(), ['accepted', 'done'])) {
                        $this->container->get('event_dispatcher')->dispatch("app.order_{$orderChange[1]->getCode()}",
                            new OrderEvent($order, false));
                    } elseif ($orderChange[1]->getCode() == 'canceled') {
                        $this->container->get('event_dispatcher')->dispatch('app.order_canceled',
                            new CancelOrderEvent($order, $orderChange[0], false));
                    }

                }
            }
        }

        $this->recomputeChanges($order);
    }

    /**
     * @param Orders $order
     *
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
            $user->setAddBonusesAt(new \DateTime());

            $this->em->persist($order);
            $this->em->persist($user);
            $this->em->flush($user);
        }
    }

    /**
     * Delete bonuses from users
     */
    public function deleteBonusesFromUsers()
    {
        // время из параметров на которое действительны бонусы
        $paramDeactivateTime = $this->container->get('options')->getParamValue('deactivateBonusesTime');

        $this->em->getRepository('AppBundle:Users')->deleteBonuses($paramDeactivateTime);
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
     * @param bool|false $preOrderFlag
     *
     * @return Orders
     * @throws CartEmptyException
     */
    protected function createOrder($sizes, $data, $user, $quickFlag = false, $preOrderFlag = false)
    {
        $order = new Orders();

        if (!$quickFlag) {
            if ($data['delivery_type']->getId() == Carriers::NP_ID) {
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
            $order->setCarriers(Arr::get($data, 'delivery_type'));
            $order->setCustomDelivery(Arr::get($data, 'customDelivery'));
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
            $quantity = $preOrderFlag ? $size->getPreOrderQuantity() : $size->getStandardQuantity();
            if ($quantity) {
                $orderSize = new OrderProductSize();
                $orderSize->setOrder($order);
                $orderSize->setQuantity($quantity);
                $orderSize->setDiscountedTotalPricePerItem($size->getDiscountedPricePerItem());
                $orderSize->setTotalPricePerItem($size->getPricePerItem());
                $orderSize->setSize($size->getSize());
                $order->addSize($orderSize);
            }
        }

        return $order;
    }

    /**
     * @param UsersEntity $user
     *
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
     * @param ProductModelSpecificSize $size
     * @return bool
     */
    public function canAddSizeToOrder(Orders $order, ProductModelSpecificSize $size)
    {
        return !($order->getPreOrderFlag() && !$size->getPreOrderFlag());
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
