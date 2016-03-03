<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\CheckAvailability;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\FollowThePrice;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class: Menus
 * @author Zimm
 */
class Menus
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
    public function main()
    {
        // Todo get categories


        return $this->templating->render('AppBundle:widgets/menus/main.html.twig', array(
                'items' => []
            )
        );
    }

    /**
     * Render menus with names started with footerMenu
     *
     * @return mixed
     */
    public function footerMenus()
    {
        $menus = $this->em->getRepository("AppBundle:Menu")->createQueryBuilder('m')
            ->where('m.name LIKE :name')
            ->setParameter('name', 'footerMenu%')
            ->orderBy('m.name')
            ->getQuery()->getResult();

        return $this->templating->render("AppBundle:widgets/menus/footer.html.twig", array(
                'menus' => $menus,
                // todo get link using MenuItemsService, create widget {{ widget 'links.get', $item }}
            )
        );
    }

}
