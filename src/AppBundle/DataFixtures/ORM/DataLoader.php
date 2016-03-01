<?php

namespace AppBundle\DataFixtures\ORM;

use Hautelook\AliceBundle\Doctrine\DataFixtures\AbstractLoader;

/**
 * Class: DataLoader
 *
 * @see AbstractLoader
 */
class DataLoader extends AbstractLoader
{
    /**
     * getFixtures
     *
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function getFixtures()
    {
        return [
            __DIR__ . '/pages.php',
            //__DIR__ . '/users.yml',
            __DIR__ . '/menu.yml',
            __DIR__ . '/menuitems.yml',
            __DIR__ . '/siteparams.yml',
            __DIR__ . '/characteristic_groups.yml',
            __DIR__ . '/characteristics.yml',
            __DIR__ . '/characteristic_values.yml',
            __DIR__ . '/categories.yml',
            __DIR__ . '/action_labels.yml',
            __DIR__ . '/products.yml',
            __DIR__ . '/products_base_categories.yml',
            __DIR__ . '/product_colors.yml',
            __DIR__ . '/product_models.yml',
            __DIR__ . '/vendors.yml',
            __DIR__ . '/products_sku.yml',
            //__DIR__ . '/filters.yml',
        ];
    }
}
