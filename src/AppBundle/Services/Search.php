<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class: Search
 * @author linux0uid
 */
class Search
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
    private $container;

    /**
     * __construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * doSearch
     *
     * @param mixed $slug
     *
     * @return void
     */
    public function doSearch($slug)
    {
        //$slug = preg_replace('/[^a-zа-я0-9\s]/i', ' AND ', $slug);
        $request = $result = [];
        $finder = $this->container->get('fos_elastica.finder.app.productModels');
        $result = $finder->find($slug);
        //dump($result);
        if (empty($result)) {
            $finder = $this->container->get('fos_elastica.finder.app.skuProducts');
            $resultSku = $finder->find($slug);
            foreach ($resultSku as $row) {
                $result[] = $row->getProductModels();
            }
        }
        foreach ($result as $model) {
            try {
                $request[] = $this->em
                    ->getRepository('AppBundle:Products')
                    ->getProductInfoByAlias($model->getAlias());
            } catch (\Doctrine\Orm\NoResultException $e) {
                //$request = null;
            }
            //dump($model->getAlias());
        }
        //dump($request);
        return $request;
    }

}
