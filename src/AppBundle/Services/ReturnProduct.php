<?php

namespace AppBundle\Services;

use AppBundle\HistoryItem\HistoryChangedItem;
use AppBundle\HistoryItem\HistoryCreatedItem;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ReturnProduct
 * @package AppBundle\Services
 * @author A. Hetmanov
 */
class ReturnProduct
{
    const HISTORY_PREFIX = 'returnProduct';

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
            'status',
            'returnDescription'
        ];
        $uow = $this->em->getUnitOfWork();

        if ($product->getId() === null) {
            (new HistoryCreatedItem($this->container, null, self::HISTORY_PREFIX))->createHistoryItem($product);
        } else {
            $productChanges = $uow->getEntityChangeSet($product);

            foreach ($productChanges as $fieldName => $productChange) {

                if ($productChange[1] == 'Заявка выполнена'){

                    $returnedBonuses = $this->container->get('prices_calculator')->getBonusesToSum($this->getReturnedSum($product->getReturnedSizes()));
                    $bonuses = $product->getUser()->getBonuses() - $returnedBonuses;
                    $product->getUser()->setBonuses($bonuses);
                }
                if ($productChange[0] == 'Заявка выполнена'){

                    $returnedBonuses = $this->container->get('prices_calculator')->getBonusesToSum($this->getReturnedSum($product->getReturnedSizes()));
                    $bonuses = $product->getUser()->getBonuses() + $returnedBonuses;
                    $product->getUser()->setBonuses($bonuses);
                }
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

    private function getReturnedSum($returnedSizes){

        $sum = 0;
        foreach($returnedSizes as $size){
            $sum += $size->getCount() * $size->getSize()->getDiscountedTotalPricePerItem();
        }
        return $sum;
    }
}
