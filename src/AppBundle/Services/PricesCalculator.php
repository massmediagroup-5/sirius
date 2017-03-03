<?php

namespace AppBundle\Services;

use AppBundle\Entity\LoyaltyProgram;
use AppBundle\Entity\Orders;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\Products;
use AppBundle\Entity\Users as User;
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
     * @var User
     */
    private $user;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     * @param EntityManager $em
     * @param User $user
     */
    public function __construct(ContainerInterface $container, EntityManager $em, $user)
    {
        $this->container = $container;
        $this->em = $em;
        $this->user = $user;
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
        // Note - no wholesale price, wholesale price used only for discounted prices

        return $object->getPrice();
    }

    /**
     * @param Products $object
     * @return float
     */
    public function getProductDiscountedPrice(Products $object)
    {
        if ($this->hasRole('ROLE_WHOLESALER')) {
            return $object->getWholesalePrice() ?: $object->getPrice();
        }

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
        // Note - no wholesale price, wholesale price used only for discounted prices

        if ($object->getPrice()) {
            return $object->getPrice();
        }
        return $this->getProductPrice($object->getProducts());
    }

    /**
     * Note: we not buy ProductModel, we buy size. ProductModel used to store common data.
     *
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelDiscountedPrice(ProductModels $object)
    {
        if ($this->hasRole('ROLE_WHOLESALER')) {
            if ($object->getWholesalePrice()) {
                return $object->getWholesalePrice();
            }
            return $this->getProductPrice($object->getProducts());
        }

        return $this->getProductModelPrice($object);
    }

    /**
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelMinDiscountedPrice(ProductModels $object)
    {
        if (($object->getSizes()->count())){
            return min(array_map(function ($size) {
                return $this->getPrice($size);
            }, $object->getSizes()->toArray()));
        }
        return false;
    }

    /**
     * @param ProductModels $object
     * @return float
     */
    public function getProductModelMaxDiscountedPrice(ProductModels $object)
    {
        if (($object->getSizes()->count())) {
            return max(array_map(function ($size) {
                return $this->getPrice($size);
            }, $object->getSizes()->toArray()));
        }
        return false;
    }

    /**
     * Return self or parent price
     *
     * @param ProductModelSpecificSize $object
     * @return float
     */
    public function getProductModelSpecificSizePrice(ProductModelSpecificSize $object)
    {
        // Note - no wholesale price, wholesale price used only for discounted prices

        if ($object->getPrice()) {
            return $object->getPrice();
        }
        return $this->getProductModelPrice($object->getModel());
    }

    /**
     * Calculate discounted price for model size
     *
     * @param ProductModelSpecificSize $object
     * @param Boolean $fromCart
     * @return float
     */
    public function getProductModelSpecificSizeDiscountedPrice(ProductModelSpecificSize $object, $fromCart = false)
    {
        if ($this->hasRole('ROLE_WHOLESALER')) {
            // Not subtract discount when user in gray list
            if ($this->hasRole('ROLE_GRAY_LIST')) {
                return $object->getPrice() ?: $this->getProductModelPrice($object->getModel());
            }

            return $object->getWholesalePrice() ?: $this->getProductModelDiscountedPrice($object->getModel());
        }

        return $this->getProductModelSpecificSizeShareDiscounted($object);
    }

    /**
     * @param ProductModelSpecificSize $object
     * @return mixed
     */
    public function getProductModelSpecificSizeShareDiscounted(ProductModelSpecificSize $object)
    {
        $price = $this->getPrice($object);

        // Subtract share discount
        $discount = $this->getProductModelSpecificSizeShareDiscount($object);

        return $discount ? $price - ceil($price * $discount) / 100 : $price;
    }

    /**
     * @param ProductModelSpecificSize $object
     * @return mixed
     */
    public function getProductModelSpecificSizeShareDiscount(ProductModelSpecificSize $object)
    {
        return $this->container->get('share')->getSingleDiscount($object);
    }

    /**
     * @return mixed
     */
    public function canHaveShareDiscount()
    {
        return !$this->hasRole('ROLE_WHOLESALER');
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
    public function getProductModelLowestSpecificSize(ProductModels $object)
    {
        $sizes = $object->getSizes()->toArray();
        usort($sizes, function ($a, $b) {
            $aPrice = $this->getProductModelSpecificSizePrice($a);
            $bPrice = $this->getProductModelSpecificSizePrice($b);
            if ($aPrice == $bPrice) {
                return 0;
            }
            return $aPrice > $bPrice ? 1 : -1;
        });
        return array_shift($sizes);
    }

    /**
     * @param ProductModelSpecificSize $object
     * @return float
     */
    public function getProductModelSpecificSizeOldPrice(ProductModelSpecificSize $object)
    {
        return $object->getOldPrice();
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
     * @param Cart $cart
     * @return float
     */
    public function getUpSellShareDiscount(Cart $cart)
    {
        $cartSizesIds = array_map(function ($size) {
            return $size->getId();
        }, $cart->getSizesEntities());

        $shares = $this->em->getRepository('AppBundle:Share')->getUpSellSharesForSizes($cartSizesIds);

        $discount = 0;
        foreach ($shares as $share) {
            if ($this->container->get('share')->isActualUpSellShare($share)) {
                // Store information about ordered sizes
                $cartSizesInGroup = [];

                // When products from each share group is in cart
                foreach ($share->getSizesGroups() as $group) {
                    $groupSizesIds = $group->getModelSpecificSizes()->map(function ($size) {
                        return $size->getId();
                    })->toArray();

                    $cartSizes = array_map(function ($size) {
                        return clone $size;
                    }, $cart->getSizes());

                    $cartSizes = array_filter(
                        $cartSizes,
                        function (CartSize $cartSize) use ($groupSizesIds) {
                            return in_array($cartSize->getSize()->getId(), $groupSizesIds);
                        }
                    );
                    $cartSizesInGroup[$group->getId()] = [
                        'sizes' => $this->orderCartSizesByPrice($cartSizes),
                        // Sum all sizes quantity
                        'quantity' => array_sum(array_map(function (CartSize $cartSize) {
                            return $cartSize->getQuantity();
                        }, $cartSizes))
                    ];
                }

                // Select min sizes quantity among all groups ordered sizes
                $minQuantity = min(array_map(function ($cartSizes) {
                    return $cartSizes['quantity'];
                }, $cartSizesInGroup));

                if ($minQuantity) {
                    // Add discount for "all groups combination"
                    if ($share->getDiscount()) {
                        // Discount for all sizes
                        $priceToDiscount = 0;
                        foreach ($share->getSizesGroups() as $group) {
                            $firstSizes = $this->getFirstSizes($cartSizesInGroup[$group->getId()]['sizes'],
                                $minQuantity);
                            foreach ($firstSizes as $cartSizeArr) {
                                $priceToDiscount += $cartSizeArr->obj->getPricePerItem() * $cartSizeArr->quantity;
                                $cartSizeArr->obj->decrementQuantity($cartSizeArr->quantity);
                            }
                        }
                        $discount += $priceToDiscount * $share->getDiscount() / 100;
                    } else {
                        // Discount for each sizes groups
                        foreach ($share->getSizesGroups() as $group) {
                            $discount += $this->decrementSizesAndCalculateDiscount($cartSizesInGroup[$group->getId()]['sizes'],
                                $minQuantity, $group->getDiscount());
                        }
                    }
                }

                // When only one combination from share group is in cart
                foreach ($share->getSizesGroups() as $index => $group) {
                    foreach ($share->getSizesGroups()->slice($index + 1) as $groupCompanion) {

                        $cartSizeArr = &$cartSizesInGroup[$group->getId()];
                        $companionCartSizeArr = &$cartSizesInGroup[$groupCompanion->getId()];

                        $minCompanionQuantity = min($cartSizeArr['quantity'],
                            $companionCartSizeArr['quantity']) - $minQuantity;

                        foreach ([$cartSizeArr, $companionCartSizeArr] as &$inLoopCartSize) {
                            $companionDiscount = $this->container->get('share')->discountForShareGroupCompanion($group,
                                $groupCompanion);
                            if ($companionDiscount) {
                                $discount += $this->decrementSizesAndCalculateDiscount($inLoopCartSize['sizes'],
                                    $minCompanionQuantity, $companionDiscount->getDiscount());
                            }
                        }
                    }
                }

                // Discount for second size
                foreach ($share->getSizesGroups() as $group) {
                    $cartSizeArr = &$cartSizesInGroup[$group->getId()];
                    $companionDiscount = $this->container->get('share')->discountForShareGroupCompanion($group,
                        $group);

                    foreach ($cartSizeArr['sizes'] as $cartSize) {
                        if ($cartSize->getQuantity() > 1) {
                            if ($companionDiscount) {
                                $discount += floor($cartSize->getQuantity() / 2) * $cartSize->getPricePerItem()
                                    * $companionDiscount->getDiscount() / 100;
                            }
                        }
                    }
                }
            }
        }

        return round($discount, 2);
    }

    /**
     * @param ProductModelSpecificSize $object
     * @return float
     */
    public function getProductModelSpecificSizeUpSellDiscountedPrice(ProductModelSpecificSize $object)
    {

        $shareGroup = $object->getShareGroup();
        $share = $shareGroup ? $shareGroup->getShare() : null;
        $price = $this->getProductModelSpecificSizeDiscountedPrice($object);

        if ($this->container->get('share')->isActualUpSellShare($share)) {
            $price -= $price * $shareGroup->getDiscount() * 0.01;
        }

        return $price;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @param ProductModelSpecificSize $companion
     * @return float
     */
    public function getProductModelSpecificSizeUpSellWithCompanionDiscountedPrice(
        ProductModelSpecificSize $size,
        ProductModelSpecificSize $companion
    ) {
        $discount = $this->container->get('share')->discountForShareGroupCompanion($size->getShareGroup(),
            $companion->getShareGroup());

        $price = $this->getDiscountedPrice($size);

        if ($discount) {
            $price -= $price * $discount->getDiscount() / 100;
        }

        return $price;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return float|int
     */
    public function hasActualShare(ProductModelSpecificSize $size)
    {
        return $this->container->get('share')->checkShareActuality($size->getShare());
    }

    /**
     * Subtract loyalty discount from sum when customer is not a wholesaler
     *
     * @param $sum
     * @param Cart $cart
     * @return number
     */
    public function getLoyaltyDiscount($sum, Cart $cart)
    {
        if ($this->hasRole('ROLE_WHOLESALER')) {
            if (!$this->hasRole('ROLE_GRAY_LIST')) {
                $user = $this->container->get('security.context')->getToken()->getUser();

                if ($this->canWholesalerHaveLoyalty()) {
                    // Use user discount
                    if ($user->getDiscount()) {
                        return $sum * $user->getDiscount() * 0.01;
                    } else {
                        // Use loyalty discount
                        return $this->getLoyaltyDiscountBySumForSum($cart->getTotalPriceForLoyalty(), $sum);
                    }
                }
            }
            return 0;
        }

        return $this->getLoyaltyDiscountBySumForSum($cart->getTotalPriceForLoyalty(), $sum);
    }

    /**
     * @return bool
     */
    public function canWholesalerHaveLoyalty()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        return $this->em->getRepository('AppBundle:Orders')->countByUserAndStatus($user, Orders::STATUS_DONE);
    }

    /**
     * @return bool
     */
    public function isWholesalerMakeOrder()
    {
        $optionsService = $this->container->get('options');

        $cart = $this->container->get('cart');

        return $cart->getDiscountedIntermediatePrice() > $optionsService->getParamValue('startWholesalerDiscount', 500);
    }

    /**
     * @param Cart $cart
     * @param $sum
     * @return mixed
     */
    public function getLoyaltyDiscountForCartForSum(Cart $cart, $sum)
    {
        return $this->getLoyaltyDiscountBySumForSum($cart->getTotalPriceForLoyalty(), $sum);
    }

    /**
     * @param $sum
     * @param $sumToDiscount
     * @return float
     */
    public function getLoyaltyDiscountBySumForSum($sum, $sumToDiscount)
    {
        $discount = $this->getLoyaltyProgramDiscountPrcBySum($sum);

        return ceil($discount * $sumToDiscount * 0.01);
    }

    /**
     * @param $sum
     * @return float|int
     */
    public function getLoyaltyProgramDiscountBySum($sum)
    {
        if ($loyaltyProgram = $this->getLoyaltyProgramBySum($sum)) {
            return ceil($loyaltyProgram->getDiscount() * $sum * 0.01);
        }
        return 0;
    }

    /**
     * @param $sum
     * @return float|int
     */
    public function getLoyaltyProgramDiscountPrcBySum($sum)
    {
        if ($loyaltyProgram = $this->getLoyaltyProgramBySum($sum)) {
            return $loyaltyProgram->getDiscount();
        }
        return 0;
    }

    /**
     * @param $sum
     * @return LoyaltyProgram|null
     */
    public function getLoyaltyProgramBySum($sum)
    {
        if ($this->hasRole('ROLE_WHOLESALER')) {
            $repo = 'AppBundle:WholesalerLoyaltyProgram';
        } else {
            $repo = 'AppBundle:LoyaltyProgram';
        }

        return $this->em->getRepository($repo)->firstBySum($sum);
    }

    /**
     * @return mixed
     */
    public function getCurrentUserLoyaltyDiscount()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        $loyaltyProgram = $this->getLoyaltyProgramBySum($user->getTotalSpent());

        return $loyaltyProgram ? $loyaltyProgram->getDiscount() : 0;
    }

    /**
     * @param $sum
     * @return int
     */
    public function getBonusesToSum($sum)
    {
        return floor($sum / 100);
    }

    /**
     * @return int
     */
    public function getMaxAllowedBonuses()
    {
        $bonusMaxPaidPercent = $this->container->get('options')->getParamValue('bonus_max_paid_percent', 0);
        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user != 'anon.') {
            $allowedBonuses = ceil($this->container->get('cart')->getDiscountedTotalPrice() * $bonusMaxPaidPercent / 100);
            $availableBonuses = ceil($user->getBonuses());
            return $availableBonuses < $allowedBonuses ? $availableBonuses : $allowedBonuses;
        }
        return 0;
    }

    /**
     * @param $cartSizes
     * @return array
     */
    private function orderCartSizesByPrice($cartSizes)
    {
        usort($cartSizes, function ($a, $b) {
            if ($a->getPricePerItem() == $b->getPricePerItem()) {
                return 0;
            }
            return ($a->getPricePerItem() < $b->getPricePerItem()) ? -1 : 1;
        });

        return $cartSizes;
    }

    /**
     * @param $cartSizes
     * @param $quantity
     * @param $discountPrc
     * @return float
     */
    private function decrementSizesAndCalculateDiscount($cartSizes, $quantity, $discountPrc)
    {
        $discountedMoney = 0;

        foreach ($this->getFirstSizes($cartSizes, $quantity) as $cartSizeArr) {
            $priceToDiscount = $cartSizeArr->obj->getPricePerItem() * $cartSizeArr->quantity;
            $discountedMoney += $priceToDiscount * $discountPrc / 100;
            $cartSizeArr->obj->decrementQuantity($cartSizeArr->quantity);
        }

        return $discountedMoney;
    }

    /**
     * Return object with cart size and quantity which satisfy $quantity param
     *
     * @param $cartSizes
     * @param $quantity
     * @return \Generator
     */
    private function getFirstSizes($cartSizes, $quantity)
    {
        foreach ($cartSizes as $cartSize) {
            $toDecrementQuantity = min($quantity, $cartSize->getQuantity());
            $quantity -= $toDecrementQuantity;
            yield (object)['quantity' => $toDecrementQuantity, 'obj' => $cartSize];
            if ($quantity == 0) {
                break;
            }
        }
    }

    /**
     * @param $role
     * @return mixed
     */
    private function hasRole($role)
    {
        return $this->user ? $this->user->hasRole($role) : false;
    }
}
