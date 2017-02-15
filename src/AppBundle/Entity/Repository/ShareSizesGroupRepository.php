<?php

namespace AppBundle\Entity\Repository;


use AppBundle\Entity\Share;

/**
 * Class ShareSizesGroupRepository
 * @package AppBundle\Entity\Repository
 */
class ShareSizesGroupRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Share $share
     * @return array
     */
    public function findActiveForShare(Share $share)
    {
        return $this->createQueryBuilder('sizesGroup')
            ->addSelect('sizes')
            ->join('sizesGroup.share', 'share')
            ->join('sizesGroup.actualModelSpecificSizes', 'sizes')
            ->where('share.id = :share')
            ->addCriteria($this->_em->getRepository('AppBundle:ProductModelSpecificSize')->getActiveCriteria())
            ->setParameter('share', $share)
            ->getQuery()
            ->getResult();
    }
}
