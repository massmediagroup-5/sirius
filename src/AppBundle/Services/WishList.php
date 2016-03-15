<?php

namespace AppBundle\Services;

use AppBundle\Entity\CheckAvailability;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\FollowThePrice;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: WishList
 * @author zimm
 */
class WishList
{

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * session
     *
     * @var mixed
     */
    private $session;

    /**
     * session
     *
     * @var mixed
     */
    private $wishList;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     * @param Session $session
     */
    public function __construct(ContainerInterface $container, Session $session)
    {
        $this->container = $container;
        $this->session = $session;

        $this->wishList = $this->session->get('wishlist') ? : [];
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->wishList;
    }

    /**
     * @param $id
     * @return array
     */
    public function toggle($id)
    {
        if(($key = array_search($id, $this->wishList)) === false) {
            $this->wishList[$id] = $id;
        } else {
            unset($this->wishList[$key]);
        }

        $this->changed();

        return $this->wishList;
    }

    /**
     * @return array
     */
    public function getCount()
    {
        return count($this->wishList);
    }

    /**
     *
     */
    protected function changed() {
        $this->session->set('wishlist', $this->wishList);
    }

}
