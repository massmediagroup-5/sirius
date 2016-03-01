<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class: Sort
 *
 */
class Sort
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
     * rebuildPriorityProductModelImages
     *
     * @param mixed $imagesIds
     */
    public function rebuildPriorityProductModelImages($imagesIds)
    {
        $i = 0;
        foreach ($imagesIds as $id) {
            if ($image = $this->em->getRepository('AppBundle:ProductModelImages')->find($id)) {
                $i++;
                $image->setPriority($i);
                $this->em->persist($image);
            }
        }
        $images = $this->em->getRepository('AppBundle:ProductModelImages')
            ->findBy(array(
                'id' => $imagesIds
            ));
        $this->em->flush();
        return $images;
    }

}
