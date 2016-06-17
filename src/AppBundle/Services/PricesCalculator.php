<?php

namespace AppBundle\Services;

use AppBundle\Entity\LoyaltyProgram;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\Products;
use AppBundle\Model\CartSize;
use Doctrine\ORM\EntityManager;
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
     * @var EntityManager
     */
    private $em;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     * @param EntityManager $em
     */
    public function __construct(ContainerInterface $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
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
        if ($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
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
        if ($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            if ($object->getWholesalePrice()) {
                return $object->getWholesalePrice();
            }
            return $this->getProductPrice($object->getProducts());
        }
        if ($object->getPrice()) {
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
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelMinDiscountedPrice(ProductModels $object)
    {
        return min(array_map(function ($size) {
            return $this->getPrice($size);
        }, $object->getSizes()->toArray()));
    }

    /**
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelMaxDiscountedPrice(ProductModels $object)
    {
        return max(array_map(function ($size) {
            return $this->getPrice($size);
        }, $object->getSizes()->toArray()));
    }

    /**
     * Return self or parent price
     *
     * @param ProductModelSpecificSize $object
     * @return float
     */
    public function getProductModelSpecificSizePrice(ProductModelSpecificSize $object)
    {
        if ($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            if ($object->getWholesalePrice()) {
                return $object->getWholesalePrice();
            }
            return $this->getProductModelPrice($object->getModel());
        }
        if ($object->getPrice()) {
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
        if ($this->authorizationChecker->isGranted('ROLE_WHOLESALER')) {
            return $this->getProductModelSpecificSizePrice($object);
        }
        $discount = $this->container->get('share')->getSingleDiscount($object);
        $price = $object->getPrice() ?: $this->getProductModelPrice($object->getModel());

        return $discount ? $price - ceil($price * $discount) / 100 : $price;
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

    /**
     * @param CartSize $object
     * @return float
     */
    public function getProductModelSpecificSizeInCartDiscountedPrice(CartSize $object)
    {
        /** @var $cart Cart */
        $cart = $this->container->get('cart');

        $shareGroup = $object->getSize()->getShareGroup();
        $share = $shareGroup ? $shareGroup->getShare() : false;
        if ($share && $share->isActive() && $share->getSizesGroups()->count() > 1) {
            $otherSizesInShare = [];
            foreach ($share->getSizesGroups() as $group) {
                $sizesIds = array_map(function ($size) {
                    return $size->getId();
                }, $cart->getSizesEntities());

                // TODO create event when cart updated and clear query cache
                $sizesInGroup = $this->em
                    ->getRepository('AppBundle:ProductModelSpecificSize')
                    ->createQueryBuilder('specificSize')
                    ->andWhere('specificSize.shareGroup = :group')
                    ->andWhere('specificSize.id IN (:specificSizesIds)')
                    ->setParameter('group', $group)
                    ->setParameter('specificSizesIds', $sizesIds)
                    ->getQuery()
                    ->useQueryCache(true)
                    ->useResultCache(true)
                    ->getResult();

                // Select max count of product sizes in action
                $sizeInGroupIds = array_map(function ($sizeInGroup) {
                    return $sizeInGroup->getId();
                }, $sizesInGroup);

                $cartSizesInGroup = array_filter($cart->getSizes(),
                    function (CartSize $cartSize) use ($sizeInGroupIds) {
                        return in_array($cartSize->getSize()->getId(), $sizeInGroupIds);
                    });

                $sizeWithMaxQuantity = array_first($cartSizesInGroup);
                foreach ($cartSizesInGroup as $cartSize) {
                    if ($cartSize->getQuantity() > $sizeWithMaxQuantity->getQuantity()) {
                        $sizeWithMaxQuantity = $cartSize;
                    }
                }

                $otherSizesInShare[$group->getId()] = $sizeWithMaxQuantity;
            }

            if (count(array_filter($otherSizesInShare)) == $share->getSizesGroups()->count()) {

                $minSizesCount = min(array_map(function (CartSize $cartSize) {
                    return $cartSize->getQuantity();
                }, $otherSizesInShare));

                $pricePerItem = $this->getDiscountedPrice($object->getSize());

                $price = ($pricePerItem - ceil($shareGroup->getDiscount() * $pricePerItem) * 0.01) * $minSizesCount
                    + $pricePerItem * ($object->getQuantity() - $minSizesCount);

                return $price;
            }
        }

        return $this->getDiscountedPrice($object->getSize()) * $object->getQuantity();
    }

    /**
     * Subtract loyalty discount from sum when customer is not a wholesaler
     *
     * @param $sum
     * @return number
     */
    public function getLoyaltyDiscounted($sum)
    {
        $isWholesaler = $this->authorizationChecker->isGranted('ROLE_WHOLESALER');

        if (!$isWholesaler && $loyaltyProgram = $this->getLoyaltyProgramBySum($sum)) {
            return $sum - ceil($loyaltyProgram->getDiscount() / 100 * $sum);
        }
        return $sum;
    }

    /**
     * @param $sum
     * @return LoyaltyProgram|null
     */
    public function getLoyaltyProgramBySum($sum)
    {
        return $this->em->getRepository('AppBundle:LoyaltyProgram')->firstBySum($sum);
    }

    /**
     * @param $sum
     * @return int
     */
    public function getBonusesToSum($sum)
    {
        return floor($sum / 100);
    }

}
