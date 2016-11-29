<?php

namespace AppBundle\Entity;

/**
 * ProductModelSpecificSizeHistory
 */
class ProductModelSpecificSizeHistory extends History
{
    /**
     * @var \stdClass
     */
    private $productModelSpecificSize;


    /**
     * Set productModelSpecificSize
     *
     * @param \AppBundle\Entity\ProductModels  $productModelSpecificSize
     *
     * @return ProductModelSpecificSizeHistory
     */
    public function setProductModelSpecificSize($productModelSpecificSize = null)
    {
        $this->productModelSpecificSize = $productModelSpecificSize;

        return $this;
    }

    /**
     * Get productModelSpecificSize
     *
     * @return \AppBundle\Entity\ProductModelSpecificSize
     */
    public function getProductModelSpecificSize()
    {
        return $this->productModelSpecificSize;
    }

    /**
     * @inheritdoc
     */
    public function getHistoriable()
    {
        return $this->productModelSpecificSize;
    }
}

