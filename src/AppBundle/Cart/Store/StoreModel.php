<?php

namespace AppBundle\Cart\Store;

class StoreModel
{
    /**
     * @var
     */
    protected $sizeId;

    /**
     * @var
     */
    protected $quantity;

    /**
     * StoreModel constructor.
     * @param $sizeId
     * @param $quantity
     */
    public function __construct($sizeId, $quantity)
    {
        $this->sizeId = $sizeId;
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return mixed
     */
    public function getSizeId()
    {
        return $this->sizeId;
    }

}