<?php

namespace AppBundle\Listener;


use AppBundle\Event\CancelOrderEvent;
use AppBundle\Event\OrderEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ChangeUserTotalSpentSubscriber
 * @package AppBundle\Listener
 */
class ChangeUserTotalSpentSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'app.order_done' => 'incrementUserTotalSpent',
            'app.order_canceled' => 'decrementUserTotalSpent',
        ];
    }

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param CancelOrderEvent $event
     */
    public function decrementUserTotalSpent(CancelOrderEvent $event)
    {
        // todo check
        if ($user = $event->getOrder()->getUsers()) {
            $user->decrementTotalSpent($event->getOrder()->getIndividualDiscountedTotalPrice());
            $this->em->persist($user);
        }
    }

    /**
     * @param OrderEvent $event
     */
    public function incrementUserTotalSpent(OrderEvent $event)
    {
        if ($user = $event->getOrder()->getUsers()) {
            $user->incrementTotalSpent($event->getOrder()->getIndividualDiscountedTotalPrice());
            $this->em->persist($user);
        }
    }
}
