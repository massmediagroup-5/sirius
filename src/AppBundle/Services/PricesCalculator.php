<?php

namespace AppBundle\Services;

use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModels;
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
        switch ($object) {
            case $object instanceof ProductModels:
                return $this->getProductModelPrice($object);
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
        switch ($object) {
            case $object instanceof ProductModels:
                return $this->getProductModelDiscountedPrice($object);
            default:
                return 0;
        }
    }

    /**
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelPrice(ProductModels $object)
    {
        if($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            return $object->getWholesalePrice() ?: $object->getPrice();
        }
        return $object->getPrice();
    }

    /**
     * Todo add actions, discounts
     *
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelDiscountedPrice(ProductModels $object)
    {
        if($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            return $object->getWholesalePrice() ?: $object->getPrice();
        }
        return $object->getPrice();
    }

    /**
     * Todo add actions, discounts
     *
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelPackageDiscountedPrice(ProductModels $object)
    {
        $oneItemPrice = $this->getProductModelDiscountedPrice($object);
        return $oneItemPrice * $object->getSizes()->count();
    }

}
