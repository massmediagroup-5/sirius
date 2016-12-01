<?php

namespace AppBundle\Services;

use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModels;
use AppBundle\HistoryItem\HistoryChangedItem;
use AppBundle\HistoryItem\HistoryCreatedItem;
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

    public function checkProductsIsOrdered(ProductModels $productModel)
    {

        return $this->em->getRepository('AppBundle:ProductModelSpecificSize')
            ->createQueryBuilder('sizes')
            ->innerJoin('sizes.orderedSizes', 'orderedSizes')
            ->where('sizes.model = :model')
            ->setParameter('model', $productModel)
            ->select('COUNT(sizes)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function processProductModelsChanges($product)
    {
        $historyPrefix = (new \ReflectionClass($product))->getShortName();
        $allowedFields = [
            'price',
        ];
        $uow = $this->em->getUnitOfWork();

        if ($product->getId() === null) {
            (new HistoryCreatedItem($this->container, null, $historyPrefix))->createHistoryItem($product, $this->container->get('security.token_storage')->getToken()->getUser());
        } else {
            $productChanges = $uow->getEntityChangeSet($product);

            foreach ($productChanges as $fieldName => $productChange) {
                if ($productChange[0] != $productChange[1]) {
                    if (in_array($fieldName, $allowedFields)) {
                        (new HistoryChangedItem($this->container, null, $historyPrefix))
                            ->createHistoryItem($product, $fieldName,
                                $productChange[0], $productChange[1], $this->container->get('security.token_storage')->getToken()->getUser());
                    }
                }
            }
        }
    }
}
