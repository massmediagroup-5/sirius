<?php

namespace AppBundle\Entity;

/**
 * ProductModelsHistory
 */
class ProductModelsHistory extends History
{

    /**
     * @var \AppBundle\Entity\ProductModels
     */
    private $productModel;

    /**
     * Set productModel
     *
     * @param \AppBundle\Entity\ProductModels $productModel
     *
     * @return ProductModelsHistory
     */
    public function setProductModels($productModel = null)
    {
        $this->productModel = $productModel;

        return $this;
    }

    /**
     * Get productModel
     *
     * @return \AppBundle\Entity\ProductModels
     */
    public function getProductModels()
    {
        return $this->productModel;
    }
}

