<?php

namespace AppBundle\Entity;

/**
 * ReturnProductHistory
 */
class ReturnProductHistory extends History
{
    /**
     * @var \AppBundle\Entity\ReturnProduct
     */
    private $returnProduct;

    /**
     * @return ReturnProduct
     */
    public function getReturnProduct()
    {
        return $this->returnProduct;
    }

    /**
     * @param ReturnProduct $returnProduct
     */
    public function setReturnProduct($returnProduct)
    {
        $this->returnProduct = $returnProduct;
    }

}

