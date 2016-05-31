<?php

namespace AppBundle\Services;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Model\CartItem;
use AppBundle\Model\CartSize;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


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
     * session
     *
     * @var mixed
     */
    private $session;

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
     * @param EntityManager $em
     * @param ContainerInterface $container
     * @param Session $session
     */
    public function __construct(EntityManager $em, ContainerInterface $container, Session $session)
    {
        $this->em = $em;
        $this->container = $container;
        $this->session = $session;
        $this->initCartFromSession();
    }

    /**
     * @param ProductModelSpecificSize $size
     * @param $quantity
     */
    public function addItemToCard(ProductModelSpecificSize $size, $quantity)
    {
        if (isset($this->items[$size->getModel()->getId()])) {
            // Each size quantity
            $this->items[$size->getModel()->getId()]->addSize($size, $quantity);
        } else {
            $this->items[$size->getModel()->getId()] = new CartItem($size->getModel(),
                $this->container->get('prices_calculator'));
            $this->items[$size->getModel()->getId()]->addSize($size, $quantity);
        }
        $this->saveInSession();
    }

    /**
     * @param ProductModels $model
     * @return $this
     */
    public function removeItem(ProductModels $model)
    {
        unset($this->items[$model->getId()]);
        $this->saveInSession();
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->items = [];
        $this->saveInSession();
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
            $this->saveInSession();
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
        $this->saveInSession();
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
        $this->saveInSession();
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
        return isset($this->items[$model->getId()]) ? $this->items[$model->getId()]->getPackagesQuantity() : 0;
    }

    /**
     * @param ProductModelSpecificSize $size
     * @return int
     */
    public function getItemSizeQuantity(ProductModelSpecificSize $size)
    {
        $item = Arr::get($this->items, $size->getModel()->getId());
        return $item ? $item->getSize($size)->getQuantity() : 0;
    }

    /**
     * @return CartItem[]
     */
    public function getPreOrderItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return $item->hasPreOrderSize();
        });
    }

    /**
     * @return CartItem[]
     */
    public function getPreOrderSizes()
    {
        $sizes = array_map(function (CartItem $item) {
            return array_filter($item->getSizes(), function (CartSize $size) {
                return $size->getSize()->getPreOrderFlag();
            });
        }, $this->items);
        return $sizes ? call_user_func_array('array_merge', $sizes) : [];
    }

    /**
     * @return CartItem[]
     */
    public function getStandardItems()
    {
        return array_filter($this->items, function (CartItem $item) {
            return !$item->hasPreOrderSize();
        });
    }

    /**
     * @return CartItem[]
     */
    public function getStandardSizes()
    {
        $items = array_map(function (CartItem $item) {
            return array_filter($item->getSizes(), function (CartSize $size) {
                return !$size->getSize()->getPreOrderFlag();
            });
        }, $this->items);
        return $items ? call_user_func_array('array_merge', $items) : [];
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getQuantity();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getStandardCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getQuantity();
        }, $this->getStandardItems()));
    }

    /**
     * @return int
     */
    public function getPreOrderCount()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getQuantity();
        }, $this->getPreOrderItems()));
    }

    /**
     * @return int
     */
    public function getStandardPrice()
    {
        return array_sum(array_map(function (CartSize $item) {
            return $item->getPrice();
        }, $this->getStandardSizes()));
    }

    /**
     * @return int
     */
    public function getStandardDiscountedPrice()
    {
        return array_sum(array_map(function (CartSize $item) {
            return $item->getDiscountedPrice();
        }, $this->getStandardSizes()));
    }

    /**
     * @return int
     */
    public function getPreOrderPrice()
    {
        return array_sum(array_map(function (CartSize $item) {
            return $item->getPrice();
        }, $this->getPreOrderSizes()));
    }

    /**
     * @return int
     */
    public function getPreOrderDiscountedPrice()
    {
        return array_sum(array_map(function (CartSize $item) {
            return $item->getDiscountedPrice();
        }, $this->getPreOrderSizes()));
    }

    /**
     * @return int
     */
    public function getTotalPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getPrice();
        }, $this->items));
    }

    /**
     * @return int
     */
    public function getDiscountedTotalPrice()
    {
        return array_sum(array_map(function (CartItem $item) {
            return $item->getDiscountedPrice();
        }, $this->items));
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->getDiscountedTotalPrice() - $this->getTotalPrice();
    }

    /**
     * @return void
     */
    public function saveInSession()
    {
        $this->session->set('cart_items', $this->toArray());
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
    public function toArrayWithExtraInfo()
    {
        $array = [];
        foreach ($this->items as $item) {
            $array[$item->getProductModel()->getId()] = [
                'id' => $item->getProductModel()->getId(),
                'packagesAmount' => $item->getPackagesQuantity()
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
    protected function initCartFromSession()
    {
        $sessionArray = $this->session->get('cart_items', []);
        $ids = [];
        foreach ($sessionArray as $item) {
            foreach ($item['sizes'] as $size) {
                $ids[] = $size['id'];
            }
        }
        $sizes = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->findWithModels($ids);
        foreach ($sizes as $size) {
            $this->addItemToCard($size, $sessionArray[$size->getModel()->getId()]['sizes'][$size->getId()]['quantity']);
        }
        $this->session->set('cart_items', $sessionArray);
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
        $this->saveInSession();
    }

}
