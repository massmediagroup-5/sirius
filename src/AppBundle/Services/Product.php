<?php

namespace AppBundle\Services;

use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class Product
 * @package AppBundle\Services
 * @author R. Slobodzian
 */
class Product
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
     * @param Orders $order
     * @param bool $flush
     * @throws \RuntimeException
     */
    public function decrementSizesQuantity(Orders $order, $flush = true)
    {
        /** @var OrderProductSize $orderSize */
        foreach ($order->getSizes() as $orderSize) {
            $size = $orderSize->getSize();
            if ($size->getQuantity() >= $orderSize->getQuantity()) {
                $size->decrementQuantity($orderSize->getQuantity());
            }
            $this->em->persist($size);
        }
        if ($flush) {
            $this->em->flush();
        }
    }

    /**
     * @param $entity
     * @param bool $flush
     * @throws \InvalidArgumentException
     */
    public function incrementSizesQuantity($entity, $flush = true)
    {
        if ($entity instanceof Orders) {
            $orderSizes = $entity->getSizes();
        } elseif ($entity instanceof OrderProductSize) {
            $orderSizes = [$entity];
        } else {
            throw new \InvalidArgumentException;
        }

        /** @var OrderProductSize $orderSize */
        foreach ($orderSizes as $orderSize) {
            $size = $orderSize->getSize();
            $size->incrementQuantity($orderSize->getQuantity());
            $this->em->persist($size);
        }
        if ($flush) {
            $this->em->flush();
        }
    }

}
