<?php

namespace AppBundle\Services;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ShareSizesGroup;
use AppBundle\Entity\ShareSizesGroupDiscount;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class: Share
 * @author zimm
 */
class Share
{

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $classNames = [
        'item_left_b',
        'item_center'
    ];

    /**
     * __construct
     *
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManager $entityManager)
    {
        $this->container = $container;
        $this->em = $entityManager;
    }

    /**
     * @param ShareSizesGroup $group
     * @param ProductModels $model
     */
    public function toggleGroupModel(ShareSizesGroup $group, ProductModels $model)
    {
        $newShareGroup = $model->inShareGroup($group) ? null : $group;

        foreach ($model->getSizes() as $size) {
            $size->setShareGroup($newShareGroup);
            $this->em->persist($size);
        }

        $this->em->flush();
    }

    /**
     * @param ShareSizesGroup $group
     * @param ProductModelSpecificSize $size
     */
    public function toggleGroupSize(ShareSizesGroup $group, ProductModelSpecificSize $size)
    {
        if ($size->getShareGroup() && $size->getShareGroup()->getId() == $group->getId()) {
            $size->setShareGroup(null);
        } else {
            $size->setShareGroup($group);
        }

        $this->em->persist($size);
        $this->em->flush();
    }

    /**
     * @param ShareSizesGroup $group
     */
    public function updateShareGroupSizes(ShareSizesGroup $group)
    {
        // Detach old sizes and models
        $this->em->createQueryBuilder()
            ->update('AppBundle:ProductModelSpecificSize', 'size')
            ->set('size.shareGroup', ':shareGroupValue')
            ->where('size.shareGroup = :shareGroup')
            ->setParameter('shareGroup', $group)
            ->setParameter('shareGroupValue', null)
            ->getQuery()
            ->execute();

        $this->em->createQueryBuilder()
            ->update('AppBundle:ProductModels', 'model')
            ->set('model.shareGroup', ':shareGroupValue')
            ->where('model.shareGroup = :shareGroup')
            ->setParameter('shareGroup', $group)
            ->setParameter('shareGroupValue', null)
            ->getQuery()
            ->execute();

        $sizes = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->getShareGroupSizes($group);

        foreach ($sizes as $size) {
            $size->setShareGroup($group);
            $this->em->persist($size);
        }

        $this->em->flush();
    }

    /**
     * Single discount only for share with one group
     * @param $entity
     * @return int|string
     */
    public function getSingleDiscount($entity)
    {
        if (!$this->container->get('security.context')->isGranted('ROLE_WHOLESALER')) {
            $shareGroup = false;

            if ($entity instanceof ProductModels) {
                $shareGroup = $entity->getSizesShareGroup();
            } elseif ($entity instanceof ProductModelSpecificSize) {
                $shareGroup = $entity->getShareGroup();
            }

            if ($shareGroup && $this->isActualSingleShare($shareGroup->getShare())) {
                return $shareGroup->getDiscount();
            }
        }

        return 0;
    }

    /**
     * Single discount only for share with one group
     *
     * @param ProductModels $entity
     *
     * @return int|string
     */
    public function getHighestPrioritySingleDiscount(ProductModels $entity)
    {
        if (!$this->container->get('security.context')->isGranted('ROLE_WHOLESALER')) {
            $shareGroup = $this->getHighestPrioritySingleShareGroup($entity);

            if ($shareGroup) {
                return $shareGroup->getDiscount();
            }
        }

        return 0;
    }

    /**
     * @param ProductModels $entity
     *
     * @return ShareSizesGroup|null
     */
    public function getHighestPrioritySingleShareGroup(ProductModels $entity)
    {
        $shareGroups = $entity->getSizes()->map(function (ProductModelSpecificSize $size) {
            return $size->getShareGroup();
        })->filter(function ($shareGroup) {
            return $shareGroup ? $this->isActualSingleShare($shareGroup->getShare()) : false;
        })->toArray();

        usort($shareGroups, function (ShareSizesGroup $a, ShareSizesGroup $b) {
            if ($a->getShare()->getPriority() == $b->getShare()->getPriority()) {
                return 0;
            }
            return $a->getShare()->getPriority() < $b->getShare()->getPriority() ? 1 : -1;
        });

        return array_first($shareGroups);
    }

    /**
     * @param array $params
     */
    public function paginateShares($params = [])
    {
        $shares = $this->em->getRepository('AppBundle:Share')->getActiveSharesQuery();

        return $this->container->get('knp_paginator')->paginate(
            $shares,
            Arr::get($params, 'current_page', 1),
            Arr::get($params, 'per_page', 7),
            ['wrap-queries' => true]
        );
    }

    /**
     * @return array
     */
    public function getNamedClassNames()
    {
        return array_combine($this->classNames, $this->classNames);
    }

    /**
     * @param \AppBundle\Entity\Share $share
     * @return bool
     */
    public function checkShareActuality(\AppBundle\Entity\Share $share = null)
    {
        if ($share) {
            $now = new \DateTime();

            $actualFlag = $share->getStartTime() <= $now && $share->getEndTime() >= $now;

            return $actualFlag && $share->isActive();
        } else {
            return false;
        }
    }

    /**
     * @param \AppBundle\Entity\Share $share
     * @return bool
     */
    public function isActualSingleShare(\AppBundle\Entity\Share $share = null)
    {
        return $this->checkShareActuality($share) && $share->getSizesGroups()->count() == 1;
    }

    /**
     * @param \AppBundle\Entity\Share $share
     * @return bool
     */
    public function isActualUpSellShare(\AppBundle\Entity\Share $share = null)
    {
        if ($this->checkShareActuality($share)) {
            // Skip empty groups
            $sizesGroups = array_filter($share->getSizesGroups()->getValues(), function (ShareSizesGroup $sizesGroup) {
                return $sizesGroup->getModelSpecificSizes()->count() > 0;
            });
            return count($sizesGroups) > 1;
        }
        return false;
    }

    /**
     * @param ShareSizesGroup $shareGroup
     * @param ShareSizesGroup $shareGroupCompanion
     * @return null|ShareSizesGroupDiscount
     */
    public function discountForShareGroupCompanion(ShareSizesGroup $shareGroup, ShareSizesGroup $shareGroupCompanion)
    {
        $discount = $shareGroup
            ->getDiscounts()
            ->filter(function (ShareSizesGroupDiscount $discount) use ($shareGroupCompanion) {
                return $discount->getCompanion()->getId() == $shareGroupCompanion->getId();
            })
            ->first();

        return $discount;
    }

    /**
     * @param ShareSizesGroup $shareGroup
     * @param ShareSizesGroup $shareGroupCompanion
     * @return float
     */
    public function discountValueForShareGroupCompanion(ShareSizesGroup $shareGroup, ShareSizesGroup $shareGroupCompanion)
    {
        $discount = $this->discountForShareGroupCompanion($shareGroup, $shareGroupCompanion);

        return $discount ? $discount->getDiscount() : 0;
    }

    /**
     * @param ShareSizesGroup $shareGroup
     * @param ShareSizesGroup $shareGroupCompanion
     * @param $discountNumber
     * @return void
     */
    public function saveDiscountForCompanion(
        ShareSizesGroup $shareGroup,
        ShareSizesGroup $shareGroupCompanion,
        $discountNumber
    ) {
        $discount = $this->discountForShareGroupCompanion($shareGroup, $shareGroupCompanion);

        if (!$discount) {
            $discount = new ShareSizesGroupDiscount();

            $discount
                ->setShareGroup($shareGroup)
                ->setCompanion($shareGroupCompanion);
        }

        $discount->setDiscount($discountNumber);
        $this->em->persist($discount);
        $this->em->flush();
    }

}
