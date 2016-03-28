<?php

namespace AppBundle\Entity;

/**
 * CartProductSize
 */
class CartProductSize
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $quantity;

    /**
     * @var \AppBundle\Entity\Cart
     */
    private $cart;

    /**
     * @var \AppBundle\Entity\ProductModelSizes
     */
    private $size;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CartProductSize
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param Cart $cart
     * @return CartProductSize
     */
    public function setCart(Cart $cart = null)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * @return \AppBundle\Entity\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param ProductModelSizes $size
     * @return CartProductSize
     */
    public function setSize(ProductModelSizes $size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return \AppBundle\Entity\Cart
     */
    public function getSize()
    {
        return $this->size;
    }



}

