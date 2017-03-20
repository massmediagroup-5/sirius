<?php

namespace AppAdminBundle\Filter;


use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;

/**
 * Class DateTimeNotStrictFilter
 *
 * @package AppAdminBundle\Filter
 */
class DateTimeNotStrictFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    protected function association(ProxyQueryInterface $queryBuilder, $data)
    {
        return [$this->getOption('alias', $queryBuilder->getRootAlias()), false];
    }

    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if ($data['value']) {
            $fieldName = $this->getOption('field_name');
            $queryBuilder->andWhere("DATE_FORMAT($alias.$fieldName, '%Y-%m-%d %H:%i') LIKE :$fieldName");
            $queryBuilder->setParameter($fieldName, "{$data['value']}%");
            $this->active = true;
        } else {
            $this->active = false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return [
            'field_type' => 'text',
            'operator_type' => 'hidden',
            'operator_options' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return [
            'sonata_type_filter_default',
            [
                'field_type' => $this->getFieldType(),
                'field_options' => $this->getFieldOptions(),
                'operator_type' => $this->getOption('operator_type'),
                'operator_options' => $this->getOption('operator_options'),
                'label' => $this->getLabel(),
            ],
        ];
    }
}
