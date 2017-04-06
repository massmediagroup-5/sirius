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
use AppBundle\Entity\Share;
use Doctrine\Common\EventSubscriber;
use AppBundle\Entity\Orders;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
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
     * @var array
     */
    protected $sharesToCheck = [];

    /**
     * @var array
     */
    protected $scheduledEntityUpdates = [];

    /**
     * Check flash allow status, needs to protect infinity loop
     *
     * @var bool
     */
    protected $needsFlushFlag = true;

    /**
     * @var bool
     */
    protected $bindActualShareGroupsToSizesFlag = false;

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
            'postFlush',
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Orders) {
            $this->container->get('order')->sendOrderEmail($entity);
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        // Update shares after flush
        if (count($this->sharesToCheck)) {
            // Protect infinity loop
            if (!$this->needsFlushFlag) {
                $this->needsFlushFlag = true;

                return;
            }
            $this->needsFlushFlag = false;

            foreach ($this->sharesToCheck as $share) {
                $this->container->get('share')->updateSharesActuality($share);
                $args->getEntityManager()->persist($share);
            }

            $this->sharesToCheck = [];
            $args->getEntityManager()->flush();
            $this->container->get('share')->bindActualShareGroupsToSizes();
        }

        if ($this->bindActualShareGroupsToSizesFlag) {
            $this->container->get('share')->bindActualShareGroupsToSizes();
            $this->bindActualShareGroupsToSizesFlag = false;
        }
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        // Protect infinity loop
        if (!$this->needsFlushFlag) {
            return;
        }

        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $updatedAndToInsert = array_merge($uow->getScheduledEntityInsertions(), $uow->getScheduledEntityUpdates());

        foreach ($uow->getScheduledCollectionUpdates() as $collection) {
            $mappings = $collection->getMapping();
            if ($mappings['fieldName'] == 'shareGroups'
                && $mappings['targetEntity'] == 'AppBundle\Entity\ShareSizesGroup'
            ) {
                $this->container->get('share')->bindActualShareGroupsToSizes();
            }
        }

        foreach ($updatedAndToInsert as $entity) {
            if ($entity instanceof Orders) {
                $this->container->get('order')->sendStatusInfo($entity);
                $this->container->get('order')->processOrderChanges($entity);
                $this->container->get('order')->recalculateOrderPrice($entity);
                $this->recomputeChanges($em, $entity);
            }
            if ($entity instanceof OrderProductSize) {
                if ($entity->getOrder()) {
                    $this->container->get('order')->recalculateOrderDiscounts($entity->getOrder());
                }

                $this->container->get('order')->recalculateOrderPrice($entity->getOrder());
                $this->recomputeChanges($em, $entity->getOrder());
            }
            if ($entity instanceof ProductModels) {
                $this->container->get('product')->processProductModelsChanges($entity);
            }
            if ($entity instanceof ProductModelSpecificSize) {

                $this->container->get('product')->processProductModelsChanges($entity);

                foreach ($entity->getShares() as $share) {
                    if (!$share->isForbidDeactivation()) {
                        $this->sharesToCheck[$share->getId()] = $share;
                    }
                }
            }
            if ($entity instanceof ReturnProduct) {
                $this->container->get('return_product')->processProductModelsChanges($entity);
            }
            if ($entity instanceof ReturnedSizes) {
                $this->container->get('returned_sizes')->processProductModelsChanges($entity);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof OrderProductSize) {
                if ($entity->getOrder()->getStatus()->getCode() != 'new') {
                    $this->container->get('product')->incrementSizesQuantity($entity, false);
                }

                if ($uow->getEntityState($entity->getOrder()) != UnitOfWork::STATE_REMOVED) {
                    $this->container->get('order')->recalculateOrderDiscounts($entity->getOrder());
                    $this->container->get('order')->recalculateOrderPrice($entity->getOrder());
                    $this->recomputeChanges($em, $entity->getOrder());
                }
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Share) {
                $this->bindActualShareGroupsToSizesFlag = true;
            }
        }

        $uow->computeChangeSets();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $this->clearCache($entity);
        }

    }


    private function clearCache($entity)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        if ($entity instanceof ProductModelImage or $entity instanceof ProductModelSpecificSize){
            $em->getRepository('AppBundle:Products')->clearGetProductInfoByAliasCache($entity->getModel());
        }
        if ($entity instanceof ProductModels){
            $em->getRepository('AppBundle:Products')->clearGetProductInfoByAliasCache($entity);
        }
        if ($entity instanceof Products or $entity instanceof ProductColors or
            $entity instanceof Categories){
            $em->getConfiguration()->getResultCacheImpl()->deleteAll();
        }
        if ($entity instanceof SiteParams){
            $this->container->get('cache')->delete('options');
        }
    }

    /**
     * @param EntityManager $em
     *
     * @param $entity
     */
    protected function recomputeChanges(EntityManager $em, $entity)
    {
        // Recompute changes
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($em->getClassMetadata(get_class($entity)), $entity);
        $em->persist($entity);
    }
}