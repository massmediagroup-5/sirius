<?php

namespace AppBundle\Widgets;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: Breadcrumbs
 * @author Zimm
 */
class Breadcrumbs
{
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
     * @var array $breadcrumbs
     */
    private $breadcrumbs = [];

    /**
     * __construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->templating = $container->get('templating');
    }

    /**
     * Get header basket
     *
     * @return mixed
     */
    public function render()
    {
        krsort($this->breadcrumbs);
        return $this->templating->render('AppBundle:widgets/breadcrumbs/breadcrumbs.html.twig', array(
                'breadcrumbs' => $this->breadcrumbs
            )
        );
    }

    public function push(array $breadcrumb) {
        $this->breadcrumbs[] = $breadcrumb;
    }


}
