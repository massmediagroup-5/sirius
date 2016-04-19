<?php

namespace AppBundle\Services;

use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\Products;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class: PricesCalculator
 * @author zimm
 */
class PricesCalculator
{

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->authorizationChecker = $this->container->get('security.authorization_checker');
    }

    /**
     * @param $object
     * @return float
     */
    public function getPrice($object)
    {
        switch (true) {
            case $object instanceof Products:
                return $this->getProductPrice($object);
            case $object instanceof ProductModels:
                return $this->getProductModelPrice($object);
            case $object instanceof ProductModelSpecificSize:
                return $this->getProductModelSpecificSizePrice($object);
            default:
                return 0;
        }
    }

    /**
     * @param $object
     * @return float
     */
    public function getDiscountedPrice($object)
    {
        switch (true) {
            case $object instanceof Products:
                return $this->getProductDiscountedPrice($object);
            case $object instanceof ProductModels:
                return $this->getProductModelDiscountedPrice($object);
            case $object instanceof ProductModelSpecificSize:
                return $this->getProductModelSpecificSizeDiscountedPrice($object);
            default:
                return 0;
        }
    }

    /**
     * @param Products $object
     * @return float
     */
    public function getProductPrice(Products $object)
    {
        if($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            return $object->getWholesalePrice() ?: $object->getPrice();
        }
        return $object->getPrice();
    }

    /**
     * Todo Currently return price without discounts, add actions, discounts
     *
     * @param Products $object
     * @return float
     */
    public function getProductDiscountedPrice(Products $object)
    {
        return $this->getProductPrice($object);
    }

    /**
     * Return self or parent price
     *
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelPrice(ProductModels $object)
    {
        if($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            if($object->getWholesalePrice()) {
                return $object->getWholesalePrice();
            }
            return $this->getProductPrice($object->getProducts());
        }
        if($object->getWholesalePrice()) {
            return $object->getPrice();
        }
        return $this->getProductPrice($object->getProducts());
    }

    /**
     * Todo Currently return price without discounts, add actions, discounts
     *
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelDiscountedPrice(ProductModels $object)
    {
        return $this->getProductModelPrice($object);
    }

    /**
     * Return self or parent price
     *
     * @param ProductModelSpecificSize $object
     * @return float
     */
    public function getProductModelSpecificSizePrice(ProductModelSpecificSize $object)
    {
        if($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            if($object->getWholesalePrice()) {
                return $object->getWholesalePrice();
            }
            return $this->getProductModelPrice($object->getModel());
        }
        if($object->getWholesalePrice()) {
            return $object->getPrice();
        }
        return $this->getProductModelPrice($object->getModel());
    }

    /**
     * @param ProductModelSpecificSize $object
     * @return float
     */
    public function getProductModelSpecificSizeDiscountedPrice(ProductModelSpecificSize $object)
    {
        return $this->getProductModelSpecificSizePrice($object);
    }

    /**
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelLowestSpecificSizePrice(ProductModels $object)
    {
        $sizes = $object->getSizes()->toArray();
        return count($sizes) ? min(array_map(function ($item) {
            return $this->getProductModelSpecificSizePrice($item);
        }, $object->getSizes()->toArray())) : 0;
    }

    /**
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelLowestSpecificSizeDiscountedPrice(ProductModels $object)
    {
        $sizes = $object->getSizes()->toArray();
        return count($sizes) ? min(array_map(function ($item) {
            return $this->getProductModelSpecificSizeDiscountedPrice($item);
        }, $sizes)) : 0;
    }

    /**
     * Todo add actions, discounts
     *
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelPackageDiscountedPrice(ProductModels $object)
    {
        $sizes = $object->getSizes()->toArray();
        return count($sizes) ? array_sum(array_map(function ($item) {
            return $this->getProductModelSpecificSizeDiscountedPrice($item);
        }, $object->getSizes()->toArray())) : 0;
    }

}
