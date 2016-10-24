<?php

namespace AppBundle\Services;

use AppBundle\HistoryItem\HistoryChangedItem;
use AppBundle\HistoryItem\HistoryCreatedItem;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ReturnedSizes
 * @package AppBundle\Services
 * @author A. Hetmanov
 */
class ReturnedSizes
{
    const HISTORY_PREFIX = 'returnedSizes';

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

    public function processProductModelsChanges($product)
    {
        $allowedFields = [
            'count',
        ];
        $uow = $this->em->getUnitOfWork();

        if ($product->getId() !== null) {
            $productChanges = $uow->getEntityChangeSet($product);

            foreach ($productChanges as $fieldName => $productChange) {

                if ($productChange[0] != $productChange[1]) {
                    if (in_array($fieldName, $allowedFields)) {
                        (new HistoryChangedItem($this->container, null, self::HISTORY_PREFIX))
                            ->createHistoryItem($product, $fieldName,
                                $productChange[0], $productChange[1], $this->container->get('security.token_storage')->getToken()->getUser());
                    }
                }
            }

        }


    }
}
