<?php

namespace AppBundle\Entity\Repository;

/**
 * BaseRepository
 *
 */
class BaseRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param $searchParameters
     * @param array $createParameters
     * @return mixed
     */
    public function findOrCreate($searchParameters, $createParameters = [])
    {
        $entity = $this->findOneBy($searchParameters);

        if(!$entity) {
            $entityClassName = $this->_entityName;
            $entity = new $entityClassName();
            $createParameters = array_merge($searchParameters, $createParameters);
            foreach($createParameters as $key => $value) {
                $setter = 'set' . ucfirst($key);
                $entity->$setter($value);
            }
            $this->_em->persist($entity);
            $this->_em->flush();
        }

        return $entity;
    }

}
