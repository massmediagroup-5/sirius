<?php

namespace AppBundle\Listener;


use AppBundle\Entity\OrderProductSize;
use Doctrine\Common\EventSubscriber;
use AppBundle\Entity\Orders;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityBeforeSaveSubscriber
 * @package AppBundle\Listener
 */
class EntityBeforeSaveSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'onFlush',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Orders) {
            $this->container->get('order')->sendOrderEmail($entity);
        }
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $updatedAndToInsert = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates());

        foreach ($updatedAndToInsert as $entity) {
            if ($entity instanceof Orders) {
                $this->container->get('order')->sendStatusInfo($entity);
                $this->container->get('order')->appendHistory($entity);
            }
        }

        $uow->computeChangeSets();
    }
}