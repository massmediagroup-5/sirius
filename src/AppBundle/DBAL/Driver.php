<?php

namespace AppBundle\DBAL;

use Doctrine\DBAL\Driver\PDOMySql\Driver as BaseDriver;

class Driver extends BaseDriver
{
    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new Platform();
    }
}
