<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class: UpdateAll
 * @author linux0uid
 */
class UpdateAll
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
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * unpublishedProductModelsExcept
     *
     * @param mixed $productModels
     */
    public function unpublishedProductModelsExcept($productModels)
    {
        $models = $this->em
            ->getRepository('AppBundle:ProductModels')
            ->findAll();

        foreach ($models as $model) {
            if ($model->getStatus() == 0)
                continue;
            if (!array_search($model->getId(), $productModels)) {
                $model->setInStock(0);
                $this->em->persist($model);
            }
        }
        $this->em->flush();
        return null;
    }

}
