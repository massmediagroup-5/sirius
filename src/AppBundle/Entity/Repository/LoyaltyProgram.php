<?php

namespace AppBundle\Entity\Repository;


/**
 * Class LoyaltyProgram
 * @package AppBundle\Entity\Repository
 */
class LoyaltyProgram extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $sum
     * @return \Doctrine\ORM\Query
     */
    public function firstBySum($sum)
    {
        return $this->createQueryBuilder('loyalty')
            ->where('loyalty.sumFrom <= :sum AND loyalty.sumTo >= :sum')
            ->setParameter('sum', $sum)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
