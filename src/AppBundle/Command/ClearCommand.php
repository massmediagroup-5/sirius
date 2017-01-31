<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class: ClearCommand
 *
 * @see ContainerAwareCommand
 */
class ClearCommand extends ContainerAwareCommand
{
    const ENTITY_NAMESPACE = 'AppBundle\Entity\\';

    /**
     * configure
     *
     */
    protected function configure()
    {
        $this
            ->setName('clear:db')
            ->setDescription('Clear ALL Database!!!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return true
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');

        $entities = [
            'ActionLabels',
            'CallBack',
//            'Categories',
            'CharacteristicGroups',
            'CharacteristicValues',
            'Characteristics',
            'DistributionSmsInfo',
            'EmailAndSmsDistribution',
            'Filters',
            'History',
            'LoyaltyProgram',
            'MainBanners',
            'Menu',
            'MenuItems',
            'OrderHistory',
            'OrderProductSize',
            'OrderSmsInfo',
//            'OrderStatus',
//            'OrderStatusPay',
            'Orders',
//            'PageImages',
//            'Pages',
            'ProductColors',
            'ProductModelImage',
            'ProductModelSizes',
            'ProductModelSpecificSize',
            'ProductModelSpecificSizeHistory',
            'ProductModels',
            'ProductModelsHistory',
            'ProductModelsHistory',
            'Products',
            'ReturnProduct',
            'ReturnProductHistory',
            'ReturnedSizes',
            'ReturnedSizesHistory',
            'Sessions',
            'Share',
            'ShareSizesGroup',
            'ShareSizesGroupDiscount',
//            'SiteParams',
            'SocialNetworks',
//            'Stores',
//            'Unisender',
//            'Users',
            'Vendors',
            'WholesalerLoyaltyProgram',
            'Wishlist'
        ];

        $relationTables = [
            'categories_has_characteristics',
            'categories_has_characteristic_values',
            'email_and_sms_distribution_users',
            'filters_has_characteristics',
            'product_models_has_recommended',
            'products_has_characteristic_values',
            'share_sizes_group_characteristic_values',
            'share_sizes_group_product_colors',
            'share_sizes_group_product_model_sizes',
        ];

        foreach ($entities as $entity) {
            $fullName = self::ENTITY_NAMESPACE . $entity;
            $q = $em->createQuery("delete from {$fullName}");
            $q->execute();
        }

        foreach ($relationTables as $table) {
            $q = $dbPlatform->getTruncateTableSql($table);
            $connection->executeUpdate($q);
        }

        $em->createQuery("delete from AppBundle\Entity\Users u where not u.username = 'admin'")
            ->execute();

        $em->createQuery("delete from AppBundle\Entity\Categories c where not c.alias = 'all'")
            ->execute();

        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
