<?php

namespace AppBundle\DBAL;

use Doctrine\DBAL\Platforms\MySqlPlatform;

class Platform extends MySqlPlatform
{
    /**
     * {@inheritdoc}
     */
    public function getTruncateTableSQL($tableName, $cascade = false)
    {
        return sprintf('SET foreign_key_checks = 0;TRUNCATE %s;SET foreign_key_checks = 1;', $tableName);
    }
}
