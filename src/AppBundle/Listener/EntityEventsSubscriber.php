<?php

namespace AppBundle\Listener;


use AppBundle\Entity\Categories;
use AppBundle\Entity\OrderProductSize;
use AppBundle\Entity\ProductColors;
use AppBundle\Entity\ProductModelImage;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\Products;
use AppBundle\Entity\ReturnedSizes;
use AppBundle\Entity\ReturnProduct;
use AppBundle\Entity\SiteParams;
use AppBundle\Factory\ChangeProcessorFactory;
use AppBundle\HistoryItem\ProductModelsHistoryRelationAddedItem;
use AppBundle\HistoryItem\ProductModelsHistoryRelationChangedItem;
use Doctrine\Common\EventSubscriber;
use AppBundle\Entity\Orders;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityEventsSubscriber
 * @package AppBundle\Listener
 */
class EntityEventsSubscriber implements EventSubscriber
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
                $this->container->get('order')->processOrderChanges($entity);
            }
            if ($entity instanceof ProductModels) {
                $this->container->get('product')->processProductModelsChanges($entity);
            }
            if ($entity instanceof ProductModelSpecificSize) {
                $this->container->get('product')->processProductModelsChanges($entity);
            }
            if ($entity instanceof ReturnProduct) {
                $this->container->get('return_product')->processProductModelsChanges($entity);
            }
            if ($entity instanceof ReturnedSizes) {
                $this->container->get('returned_sizes')->processProductModelsChanges($entity);
            }
            $this->clearCache($entity);
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof OrderProductSize) {
                if ($entity->getOrder()->getStatus()->getCode() != 'new') {
                    $this->container->get('product')->incrementSizesQuantity($entity, false);
                }
            }
        }

        $uow->computeChangeSets();
    }

    private function clearCache($entity)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        switch (get_class($entity)) {
            case ProductModelImage::class:
            case ProductModelSpecificSize::class:
                $em->getRepository('AppBundle:Products')->clearGetProductInfoByAliasCache($entity->getModel());
                break;
            case ProductModels::class:
                $em->getRepository('AppBundle:Products')->clearGetProductInfoByAliasCache($entity);
                break;
            case Products::class:
            case ProductColors::class:
            case Categories::class:
                $em->getConfiguration()->getResultCacheImpl()->deleteAll();
                break;
            case SiteParams::class:
                $this->container->get('cache')->delete('options');
                break;
        }
    }
}