<?php

namespace AppBundle\Entity\Repository;


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
     * @param $group
     * @param $groupAlias
     */
    public function addHasGroupCondition(QueryBuilder $builder, $group, $groupAlias = 'model')
    {
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

    /**
     * @param QueryBuilder $builder
     * @param $group
     */
    public function addNotHasGroupExceptGivenCondition(QueryBuilder $builder, $group)
    {
        $builder->andWhere(
            $builder->expr()->orX(
                $builder->expr()->eq('specificSizes.shareGroup', $builder->expr()->literal($group->getId())),
                $builder->expr()->isNull('specificSizes.shareGroup')
            )
        );
    }

    /**
     * @param QueryBuilder $builder
     * @param $group
     */
    public function addHasGroupExceptGivenCondition(QueryBuilder $builder, $group)
    {
        $builder->andWhere('specificSizes.shareGroup <> :group')
            ->andWhere('specificSizes.shareGroup IS NOT NULL')
            ->setParameter('group', $group);
    }

}
