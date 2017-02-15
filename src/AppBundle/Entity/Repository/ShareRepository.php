<?php

namespace AppBundle\Entity\Repository;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ShareRepository
 * @package AppBundle\Entity\Repository
 */
class ShareRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * Add condition to select only models which related to given group by filters
     *
     * @param QueryBuilder $builder
     * @param \AppBundle\Entity\ShareSizesGroup $group
     * @param boolean $dirtyGroupFlag
     */
    public function addHasGroupCondition(QueryBuilder $builder, $group, $dirtyGroupFlag = false)
    {
        if ($dirtyGroupFlag) {
            if ($group->getCharacteristicValues()->count()) {
                $builder->andWhere('characteristicValues.id IN (:characteristicValues)')
                    ->groupBy('specificSizes.id')
                    ->having('COUNT(DISTINCT characteristics.id) >=
                    (
                        SELECT COUNT( DISTINCT incchar.id )
                        FROM \AppBundle\Entity\Characteristics as incchar
                        JOIN incchar.characteristicValues as inccharval
                        WHERE inccharval.id IN (:characteristicValues)
                    )'
                    )
                    ->setParameter('characteristicValues', $group->getCharacteristicValues()->map(function ($item) {
                        return $item->getId();
                    })->toArray());
            }
            if ($group->getSizes()->count()) {
                $builder->andWhere('size.id IN (:sizes)')
                    ->setParameter('sizes', $group->getSizes()->map(function ($item) {
                        return $item->getId();
                    })->toArray());
            }
            if ($group->getColors()->count()) {
                $builder->andWhere('color.id IN (:colors)')
                    ->setParameter('colors', $group->getColors()->map(function ($item) {
                        return $item->getId();
                    })->toArray());
            }
        } else {
            $builder
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->not($builder->expr()->exists(
                            'SELECT c_characteristic_value_1.id FROM \AppBundle\Entity\ShareSizesGroup c_group_1
                        JOIN c_group_1.characteristicValues c_characteristic_value_1
                        WHERE c_group_1 = :groupEntity'
                        )),
                        $builder->expr()->in('characteristicValues.id',
                            'SELECT c_characteristic_value_1_2.id FROM \AppBundle\Entity\ShareSizesGroup c_group_1_2
                        JOIN c_group_1_2.characteristicValues c_characteristic_value_1_2
                        WHERE c_group_1_2 = :groupEntity'
                        )
                    )
                )
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->not($builder->expr()->exists(
                            'SELECT c_size_2.id FROM \AppBundle\Entity\ShareSizesGroup c_group_2
                        JOIN c_group_2.sizes c_size_2
                        WHERE c_group_2 = :groupEntity'
                        )),
                        $builder->expr()->in('size.id',
                            'SELECT c_size_2_2.id FROM \AppBundle\Entity\ShareSizesGroup c_group_2_2
                        JOIN c_group_2_2.sizes c_size_2_2
                        WHERE c_group_2_2 = :groupEntity'
                        )
                    )
                )
                ->andWhere(
                    $builder->expr()->orX(
                        $builder->expr()->not($builder->expr()->exists(
                            'SELECT c_color_3.id FROM \AppBundle\Entity\ShareSizesGroup c_group_3
                        JOIN c_group_3.colors c_color_3
                        WHERE c_group_3 = :groupEntity'
                        )),
                        $builder->expr()->in('color.id',
                            'SELECT c_color_3_2.id FROM \AppBundle\Entity\ShareSizesGroup c_group_3_2
                        JOIN c_group_3_2.colors c_color_3_2
                        WHERE c_group_3_2 = :groupEntity'
                        )
                    )
                )
                ->groupBy('specificSizes.id')
                ->having('COUNT(DISTINCT characteristics.id) >=
                    (
                        SELECT COUNT( DISTINCT incchar.id )
                        FROM \AppBundle\Entity\Characteristics as incchar
                        JOIN incchar.characteristicValues as inccharval
                        WHERE inccharval.id IN (
                            SELECT c_characteristic_value.id FROM \AppBundle\Entity\ShareSizesGroup c_group
                            JOIN c_group.characteristicValues c_characteristic_value
                            WHERE c_group = :groupEntity
                        )
                    )'
                )
                ->setParameter('groupEntity', $group);
        }
    }

    /**
     * @return QueryBuilder
     */
    public function getActiveSharesQuery()
    {
        return $this->createQueryBuilder('share')
            ->where('share.status = 1 AND share.startTime < :today AND share.endTime > :today')
            ->setParameter('today', new \DateTime())
            ->getQuery();
    }

    /**
     * @param $sizesIds
     * @return array
     */
    public function getUpSellSharesForSizes($sizesIds)
    {
        return $this->createQueryBuilder('share')
            ->join('share.sizesGroups', 'groups')
            ->join('groups.actualModelSpecificSizes', 'sizes')
            ->where('sizes.id IN (:ids)')
            ->addCriteria($this->getActiveCriteria())
            ->setParameter('ids', $sizesIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param QueryBuilder $builder
     * @param $group
     */
    public function addHasGroupExceptGivenCondition(QueryBuilder $builder, $group)
    {
        $builder->andWhere('shareGroups.id <> :group')
            ->andWhere('shareGroups.id IS NOT NULL')
            ->setParameter('group', $group);
    }

    /**
     * @param string $alias
     * @return Criteria
     */
    public function getActiveCriteria($alias = 'share')
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq("$alias.status", 1))
            ->andWhere(Criteria::expr()->lt("$alias.startTime", new \DateTime()))
            ->andWhere(Criteria::expr()->gt("$alias.endTime", new \DateTime()));
    }

}
