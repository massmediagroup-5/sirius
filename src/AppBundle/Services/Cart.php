<?php

namespace AppBundle\Services;

use AppBundle\Cart\Store\CartStoreInterface;
use AppBundle\Cart\Store\StoreModel;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Model\CartItem;
use AppBundle\Model\CartSize;
use Doctrine\ORM\EntityManager;
use AppBundle\Helper\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class: Cart
 * @author R. Slobodzian
 */
class Cart
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * container
     *
     * @var mixed
     */
    protected $container;

    /**
     * CartStoreInterface
     *
     * @var mixed
     */
    private $store;

    /**
     * Items objects list
     *
     * @var CartItem[]
     */
    protected $items = [];

    /**
     * Items objects list
     *
     * @var CartItem[]
     */
    protected $backup = null;

    /**
     * Cart constructor.
     * @param EntityManager $em
     * @param ContainerInterface $container
     * @param CartStoreInterface $store
     */
    public function __construct(EntityManager $em, ContainerInterface $container, CartStoreInterface $store)
    {
        $this->em = $em;
        $this->container = $container;
        $this->store = $store;
        $this->pricesCalculator = $this->container->get('prices_calculator');
        $this->initCartFromStore();
    }

    /**
     * @param ProductModelSpecificSize $size
     * @param $quantity
     */
    public function addItemToCard(ProductModelSpecificSize $size, $quantity)
    {
        $this->addItem($size, $quantity);
        $this->saveInStore();
    }

    /**
     * @param ProductModels $model
     * @return $this
     */
    public function removeItem(ProductModels $model)
    {
        unset($this->items[$model->getId()]);
        $this->saveInStore();
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->items = [];
        $this->saveInStore();
        return $this;
    }

    /**
     * @param $size
     * @return $this
     */
    public function removeItemSize(ProductModelSpecificSize $size)
    {
        if (isset($this->items[$size->getModel()->getId()])) {
            $this->items[$size->getModel()->getId()]->removeSize($size);
            if (!$this->items[$size->getModel()->getId()]->getQuantity()) {
                $this->removeItem($size->getModel());
            }
            $this->saveInStore();
        }
        return $this;
    }

    /**
     * @param ProductModelSpecificSize $oldSize
     * @param ProductModelSpecificSize $newSize
     * @return $this
     */
    public function changeItemSize(ProductModelSpecificSize $oldSize, ProductModelSpecificSize $newSize)
    {
        $this->items[$oldSize->getModel()->getId()]->changeSize($oldSize, $newSize);
        $this->saveInStore();
        return $this;
    }

    /**
     * @param $size
     * @param $quantity
     * @return $this
     */
    public function changeItemSizeCount(ProductModelSpecificSize $size, $quantity)
    {
        $this->items[$size->getModel()->getId()]->setSize($size, $quantity);
        $this->saveInStore();
        return $this;
    }

    /**
     * @param ProductModels $model
     * @return bool
     */
    public function inCart(ProductModels $model)
    {
        return isset($this->items[$model->getId()]);
    }

    /**
     * @return CartItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return ProductModelSpecificSize[]
     */
    public function getSizesEntities()
    {
        $sizes = [];
        foreach ($this->items as $item) {
            $sizes = array_merge($sizes, $item->getSizesEntities());
        }

        return $sizes;
    }

    /**
     * @return CartSize[]
     */
    public function getSizes()
    {
        $sizes = array_map(function (CartItem $item) {
            return $item->getSizes();
        }, $this->items);

        return $sizes ? call_user_func_array('array_merge', $sizes) : [];
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !count($this->items);
    }

    /**
     * @return bool
     */
    public function hasValidQuantity()
    {
        foreach ($this->getSizes() as $size) {
            if ($size->getQuantity() > $size->getSize()->getQuantity() && !$size->getSize()->getPreOrderFlag()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return CartItem[]
     */
    public function getModels()
    {
        return array_map(function (CartItem $item) {
            return $item->getProductModel();
        }, $this->items);
    }

    /**
     * @param ProductModels $model
     * @return CartItem[]
     */
    public function getItem(ProductModels $model)
    {
        return Arr::get($this->items, $model->getId());
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return CartSize
     */
    public function getSizeDiscountedPrice(ProductModelSpecificSize $size)
    {
        $cartItem = $this->getItem($size->getModel());
        if (!$cartItem) {
            return 0;
        }
        $size = $cartItem->getSize($size);
        return $size ? $size->getDiscountedPrice() : 0;
    }

    /**
     * @param ProductModels $model
     * @return int
     */
    public function getItemQuantity(ProductModels $model)
    {
        return isset($this->items[$model->getId()]) ? $this->items[$model->getId()]->getQuantity() : 0;
    }

    /**
     * @param ProductModels $model
     * @return int
     */
    public function getItemPackagesQuantity(ProductModels $model)
    {
        return isset($this->items[$model->getId()]) ? $this->items[$model->getId()]->getAllPackagesQuantity() : 0;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function getItemSizeQuantity(ProductModelSpecificSize $size)
    {
        $item = Arr::get($this->items, $size->getModel()->getId());
        $size = $item ? $item->getSize($size) : 0;
        return $size ? $size->getQuantity() : 0;
    }

    /**
     * @return CartItem[]
     */
    public function hasPreOrderSizes()
    {
        foreach ($this->items as $item) {
            foreach ($item->getSizes() as $size) {
                if ($size->getPreOrderQuantity()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return Arr::sumProperty($this->items, 'quantity');
    }

    /**
     * @return int
     */
    public function getStandardCount()
    {
        return Arr::sumProperty($this->items, 'standardSizesQuantity');
    }

    /**
     * @return int
     */
    public function getPreOrderCount()
    {
        return Arr::sumProperty($this->items, 'preOrderSizesQuantity');
    }

    /**
     * @return int
     */
    public function getStandardPrice()
    {
        return Arr::sumProperty($this->getSizes(), 'standardPrice');
    }

    /**
     * @return int
     */
    public function getStandardDiscountedPrice()
    {
        return Arr::sumProperty($this->getSizes(), 'standardDiscountedPrice');
    }

    /**
     * @return int
     */
    public function getPreOrderPrice()
    {
        return Arr::sumProperty($this->getSizes(), 'preOrderPrice');
    }

    /**
     * @return int
     */
    public function getPreOrderDiscountedPrice()
    {
        return Arr::sumProperty($this->getSizes(), 'preOrderDiscountedPrice');
    }

    /**
     * @return int
     */
    public function getTotalPrice()
    {
        return Arr::sumProperty($this->items, 'price');
    }

    /**
     * @return int
     */
    public function getDiscountedTotalPrice()
    {
        $price = $this->pricesCalculator->getLoyaltyDiscounted($this->getDiscountedIntermediatePrice());
        $price = $price - $this->pricesCalculator->getUpSellShareDiscount($this);

        return $price;
    }

    /**
     * Return discounted price without loyalty discount
     *
     * @return int
     */
    public function getDiscountedIntermediatePrice()
    {
        return Arr::sumProperty($this->items, 'discountedPrice');
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->getDiscountedTotalPrice() - $this->getTotalPrice();
    }

    /**
     * @return float
     */
    public function getDiscountPercentages()
    {
        $discount = $this->getDiscount();
        $totalPrice = $this->getTotalPrice();

        return $totalPrice ? round($discount * 100 / $this->getTotalPrice()) : 0;
    }

    /**
     * @return void
     */
    public function saveInStore()
    {
        $this->store->setSizes($this->toSizesArray());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->items as $item) {
            if ($item->getQuantity() > 0) {
                $array[$item->getProductModel()->getId()] = [
                    'id' => $item->getProductModel()->getId(),
                ];
                $sizes = [];
                foreach ($item->getSizes() as $size) {
                    $sizes[$size->getSize()->getId()] = [
                        'id' => $size->getSize()->getId(),
                        'quantity' => $size->getQuantity()
                    ];
                }
                $array[$item->getProductModel()->getId()]['sizes'] = $sizes;
            } else {
                unset($this->items[$item->getProductModel()->getId()]);
            }
        }
        return $array;
    }

    /**
     * @return array
     */
    public function toSizesArray()
    {
        $array = [];
        foreach ($this->getSizes() as $item) {
            $array[] = new StoreModel($item->getSize()->getId(), $item->getQuantity());
        }
        return $array;
    }

    /**
     * @return array
     */
    public function toArrayWithExtraInfo()
    {
        $array = [];
        foreach ($this->items as $item) {
            $array[$item->getProductModel()->getId()] = [
                'id' => $item->getProductModel()->getId(),
                'packagesAmount' => $item->getAllPackagesQuantity()
            ];
            $sizes = [];
            foreach ($item->getSizes() as $size) {
                $sizes[$size->getSize()->getId()] = [
                    'id' => $size->getSize()->getId(),
                    'quantity' => $size->getQuantity()
                ];
            }
            $array[$item->getProductModel()->getId()]['sizes'] = $sizes;
        }
        return $array;
    }

    /**
     * @return void
     */
    protected function initCartFromStore()
    {
        $storeArray = $this->store->getSizes();
        $ids = [];
        foreach ($storeArray as $storeItem) {
            $ids[] = $storeItem->getSizeId();
        }
        $sizes = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->findWithModels($ids);
        foreach ($sizes as $size) {
            $storeItem = Arr::first($storeArray, function ($key, $storeItem) use ($size) {
                return $storeItem->getSizeId() == $size->getId();
            });
            if ($storeItem) {
                $this->addItem($size, $storeItem->getQuantity());
            }
        }
    }

    /**
     *
     */
    public function backupCart()
    {
        $this->backup = $this->items;
    }

    /**
     *
     */
    public function restoreCartFromBackup()
    {
        $this->items = $this->backup;
        unset($this->backup);
        $this->saveInStore();
    }

    /**
     * @param ProductModelSpecificSize $size
     * @param $quantity
     */
    protected function addItem(ProductModelSpecificSize $size, $quantity)
    {
        if (isset($this->items[$size->getModel()->getId()])) {
            // Each size quantity
            $this->items[$size->getModel()->getId()]->addSize($size, $quantity);
        } else {
            $this->items[$size->getModel()->getId()] = new CartItem($size->getModel(), $this->pricesCalculator);
            $this->items[$size->getModel()->getId()]->addSize($size, $quantity);
        }
    }

}
