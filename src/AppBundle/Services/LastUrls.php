<?php

namespace AppBundle\Services;

use AppBundle\Entity\ProductModelSizes;
use AppBundle\Entity\SkuProducts;
use AppBundle\Model\CartItem;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: LastUrls
 * @author R. Slobodzian
 */
class LastUrls
{
    /**
     * @var mixed
     */
    private $session;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param Session $session
     */
    public function __construct(ContainerInterface $container, Session $session)
    {
        $this->session = $session;
        $this->container = $container;
    }

    /**
     * @param $url
     */
    public function setLastCatalogUrl($url) {
        $this->session->set('last_catalog_url', $url);
    }

    /**
     * @return string
     */
    public function getLastCatalogUrl() {
        return $this->session->get('last_catalog_url', '/');
    }

    /**
     * @param $url
     */
    public function setLastRequestedUrl($url) {
        $this->session->set('last_requested_url', $url);
    }

    /**
     * @return string
     */
    public function getLastRequestedUrl() {
        return $this->session->get('last_requested_url', '/');
    }

}
