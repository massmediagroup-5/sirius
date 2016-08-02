<?php

namespace AppBundle\Services;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\ORM\EntityManager;

/**
 * Class: Options
 *
 */
class Options
{
    /**
     * @var string
     */
    private $cacheKey = 'options';

    /**
     * params
     *
     * @var mixed
     */
    private $params;

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * Options constructor.
     * @param EntityManager $em
     * @param CacheProvider $cache
     */
    public function __construct(EntityManager $em, CacheProvider $cache)
    {
        $this->em = $em;
        $this->cache = $cache;
    }

    /**
     * getParams
     *
     * @return array
     */
    public function getParams()
    {
        if (empty($this->params)) {
            if (!$this->params = $this->cache->fetch($this->cacheKey)) {
                $params = $this->em->getRepository('AppBundle:SiteParams')->findAll();
                foreach ($params as $value) {
                    $this->params[$value->getParamName()] = array(
                        'value' => $value->getParamValue(),
                        'active' => $value->getActive()
                    );
                }

                $this->cache->save($this->cacheKey, $this->params);
            }
        }
        return $this->params;
    }

    /**
     * @param $name
     * @param bool $default
     *
     * @return mixed
     */
    public function getParamValue($name, $default = false)
    {
        return isset($this->getParams()[$name]['value']) ? $this->params[$name]['value'] : $default;
    }

    /**
     * @return array
     */
    public function getSocialIcons()
    {
        return $this->em->getRepository('AppBundle:SocialNetworks')->findBy(['active' => 1]);
    }

}
