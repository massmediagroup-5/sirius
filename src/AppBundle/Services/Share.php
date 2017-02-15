<?php

namespace AppBundle\Services;

use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ShareSizesGroup;
use AppBundle\Entity\ShareSizesGroupDiscount;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
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
        'item_center',
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
        $inShareGroup = $model->inShareGroup($group) ? null : $group;

        foreach ($model->getSizes() as $size) {
            if ($inShareGroup) {
                $size->addShareGroup($group);
            } else {
                $size->removeShareGroup($group);
            }
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
        $size->toggleShareGroup($group);

        $this->em->persist($size);
        $this->em->flush();
    }

    /**
     * @param ShareSizesGroup $group
     */
    public function updateShareGroupSizes(ShareSizesGroup $group)
    {
        // Detach old sizes and models
        $this->em->getConnection()->executeUpdate(
            'DELETE FROM `product_model_specific_size_share_sizes_group` WHERE `share_sizes_group_id` = :shareGroup',
            ['shareGroup' => $group->getId()]
        );

        $sizes = $this->em->getRepository('AppBundle:ProductModelSpecificSize')->getShareGroupSizes($group);

        foreach ($sizes as $size) {
            $size->addShareGroup($group);
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
                return $sizesGroup->getActualModelSpecificSizes()->count() > 0;
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

    /**
     * Bind actual share groups to sizes
     */
    public function bindActualShareGroupsToSizes()
    {
        $this->em->getConnection()->executeUpdate(
            'UPDATE product_model_specific_size s
            SET share_group_id = (
                SELECT ssg.id FROM share_sizes_group ssg
                JOIN product_model_specific_size_share_sizes_group s_ssg
                    ON s_ssg.share_sizes_group_id = ssg.id
                JOIN share ON ssg.share_id = share.id
                WHERE share.status = 1 AND share.start_time < NOW() AND share.end_time > NOW()
                    AND s_ssg.product_model_specific_size_id = s.id
                ORDER BY share.priority DESC LIMIT 1
        )');
    }

    /**
     * Deactivate share when all discounts is defective (that is mean that main group discount is also not active)
     *
     * @param \AppBundle\Entity\Share $share
     */
    public function updateSharesActuality(\AppBundle\Entity\Share $share)
    {
        $discounts = $share->getActualGroupsDiscounts();

        /** @var ShareSizesGroupDiscount $discount */
        foreach ($discounts as $discount) {
            $availability = $this->em->getRepository('AppBundle:ProductModelSpecificSize')
                ->isHasShareGroupHasAvailableSizes($discount->getShareGroup());
            if ($availability) {
                $companionAvailability = $this->em->getRepository('AppBundle:ProductModelSpecificSize')
                    ->isHasShareGroupHasAvailableSizes($discount->getCompanion());
                if ($companionAvailability) {
                    // At least one discount has sizes
                    return;
                }
            }
        }

        $share->setStatus(false);
    }

}
