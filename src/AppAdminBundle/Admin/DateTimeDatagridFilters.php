<?php

namespace AppAdminBundle\Admin;


/**
 * Class DateTimeDatagridFilters
 *
 * @package AppAdminBundle\Admin
 */
trait DateTimeDatagridFilters
{
    /**
     * @param $queryBuilder
     * @param $alias
     * @param $fieldName
     * @param $value
     */
    public function filterByDateTime($queryBuilder, $alias, $fieldName, $value)
    {
        if ($value['value']) {
            $queryBuilder->andWhere("DATE_FORMAT($alias.$fieldName, '%Y-%m-%d %H:%i') LIKE :$fieldName");
            $queryBuilder->setParameter($fieldName, "{$value['value']}%");
        }
    }
}
