<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\CheckAvailability;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\FollowThePrice;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class: Sliders
 * @author Zimm
 */
class Sliders
{
    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * templating
     *
     * @var mixed
     */
    private $templating;

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
        $this->templating = $container->get('templating');
    }

    /**
     * Get header basket
     *
     * @return mixed
     */
    public function home()
    {
        $slides = $this->em->getRepository('AppBundle:MainSlider')->findBy(
            array('active'=>1)
        );

        return $this->templating->render('AppBundle:widgets/sliders/home.html.twig', array(
                'slides' => $slides
            )
        );
    }
}
