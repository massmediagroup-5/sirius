<?php

namespace AppBundle\Entity\Repository;

/**
 * Class: CharacteristicValuesRepository
 *
 * @see \Doctrine\ORM\EntityRepository
 */
class MenuRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * getValuesByCharacteristic
     *
     * @param string $name
     * @return mixed
     */
    public function oneByName($name)
    {
        $query_obj = $this->createQueryBuilder('menu')
            ->where("menu.name = :name")->setParameter('name', $name)
            ->setMaxResults(1);
        return $query_obj->getQuery()->getOneOrNullResult();
    }

}
