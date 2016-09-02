<?php

namespace AppBundle\Cart\Store;


use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Interface CartStoreInterface
 * @package AppBundle\Cart\Store
 */
class SessionCartStore implements CartStoreInterface
{
    /**
     * @var Session
     */
    protected $session;

    protected $sessionKey = 'cart_items';

    /**
     * SessionCartStore constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getSizes()
    {
        $array = [];
        foreach ($this->session->get($this->sessionKey, []) as $size) {
            $array[] = new StoreModel($size['size_id'], $size['quantity']);
        }
        return $array;
    }

    /**
     * @inheritdoc
     */
    public function setSizes(array $sizes)
    {
        $array = [];
        /** @var StoreModel $size */
        foreach ($sizes as $size) {
            $array[] = [
                'size_id' => $size->getSizeId(),
                'quantity' => $size->getQuantity()
            ];
        }
        $this->session->set($this->sessionKey, $array);
    }
}
