<?php

namespace AppBundle\Services;

use AppBundle\Entity\CheckAvailability;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ShareSizesGroup;
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

}
