<?php

namespace AppBundle\Services;

use AppBundle\Entity\History;
use AppBundle\HistoryItem\AbstractHistoryItem;
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
     * @param History $history
     */
    public function add(History $history)
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
        return $this->createFromHistoryItem($this->em->getRepository('AppBundle:History')->find($id));
    }

    /**
     * @param History $history
     * @return AbstractHistoryItem
     */
    public function createFromHistoryItem(History $history)
    {
        $itemClassName = $history->getChangeType();
        return new $itemClassName($this->container, $history);
    }
}
