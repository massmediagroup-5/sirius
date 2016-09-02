<?php

namespace AppAdminBundle\Services;

use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModels;

/**
 * Class WholesalerOrder
 * @package AppAdminBundle\Transformer
 */
class WholesalerCart
{
    /**
     * @var Orders
     */
    protected $order;

    /**
     * @param Orders $order
     */
    public function setOrder(Orders $order)
    {
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function getModels()
    {
        $models = [];
        foreach ($this->order->getSizes() as $size) {
            if (!isset($models[$size->getSize()->getModel()->getId()])) {
                $models[$size->getSize()->getModel()->getId()] = $size->getSize()->getModel();
            }
        }
        return $models;
    }

    /**
     * @param ProductModels $model
     * @return array
     */
    public function getPackagesCount($model)
    {
        $availableSizesIds = $model->getSizes()->map(function ($size) {
            return $size->getId();
        })->toArray();
        $currentSizesIds = $this->order->getSizes()->map(function ($item) {
            return $item->getSize()->getId();
        })->toArray();

        // If array equals in cart all available model sizes.
        if (empty(array_diff($availableSizesIds, $currentSizesIds))) {
            // Packages count - is minimal amount of concrete size.
            // When we have only one size "52-54" - we can`n have more then one package.
            $packagesCount = min(array_map(function (OrderProductSize $size) {
                return $size->getQuantity();
            }, $this->getModelSizes($model)));
            return $packagesCount;
        }
        return 0;
    }

    /**
     * @param ProductModels $model
     * @return array
     */
    public function getModelSizes($model)
    {
        $sizes = [];
        foreach ($this->order->getSizes() as $size) {
            if ($size->getSize()->getModel()->getId() == $model->getId()) {
                $sizes[] = $size;
            }
        }
        return $sizes;
    }

    /**
     * @param ProductModels $model
     * @return array
     */
    public function getSingleSizes($model)
    {
        $sizes = [];
        $packagesCount = $this->getPackagesCount($model);
        foreach ($this->getModelSizes($model) as $size) {
            $diff = $size->getQuantity() - $packagesCount;
            if ($diff > 0) {
                $sizes[] = [
                    'quantity' => $diff,
                    'entity' => $size,
                ];
            }
        }
        return $sizes;
    }

    /**
     * @return Orders
     */
    public function getOrder()
    {
        return $this->order;
    }
}
