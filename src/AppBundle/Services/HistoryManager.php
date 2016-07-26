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
use AppBundle\HistoryItem\AbstractHistoryItem;
use AppBundle\HistoryItem\OrderHistoryMoveSizeCommand;
use AppBundle\HistoryItem\OrderHistoryMoveToSizeItem;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class HistoryItemsManager
 * @package AppBundle\Services
 */
class HistoryManager
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
     * @param ContainerInterface $container
     * @param EntityManager $em
     */
    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    /**
     * @param OrderHistory $history
     */
    public function add(OrderHistory $history)
    {
        $this->em->persist($history);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @return AbstractHistoryItem
     */
    public function createFromId($id)
    {
        return $this->createFromHistoryItem($this->em->getRepository('AppBundle:OrderHistory')->find($id));
    }

    /**
     * @param OrderHistory $history
     * @return AbstractHistoryItem
     */
    public function createFromHistoryItem(OrderHistory $history)
    {
        $itemClassName = $history->getChangeType();
        return new $itemClassName($this->container, $history);
    }
}
