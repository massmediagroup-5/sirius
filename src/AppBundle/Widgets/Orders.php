<?php

namespace AppBundle\Widgets;

use AppBundle\Entity\History;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Form\Type\ChangeProductSizeQuantityType;
use AppBundle\Form\Type\ChangeProductSizeType;
use AppBundle\Form\Type\RemoveProductSizeType;
use AppBundle\Model\CartSize;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Form\Type\AddInCartType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Class: Orders
 * @author Zimm
 */
class Orders
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
     * @param History $history
     * @return mixed
     */
    public function historyItem(History $history)
    {
        $history = $this->container->get('history_manager')->createFromHistoryItem($history);

        return $this->templating->render('AppBundle:widgets/order/history_item.html.twig', compact('history'));
    }

}
