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
     * @throws \RuntimeException
     */
    public function updateSizesQuantity(Orders $order)
    {
        /** @var OrderProductSize $orderSize */
        foreach ($order->getSizes() as $orderSize) {
            $size = $orderSize->getSize();
            if ($size->getQuantity() >= $orderSize->getQuantity()) {
                $size->decrementQuantity($orderSize->getQuantity());
                // Todo set countedQuantityFlag?
                $orderSize->setCountedQuantityFlag();
            }
            $this->em->persist($size);
        }
        $this->em->flush();
    }

}
