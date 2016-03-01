<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class: ClearDb
 *
 */
class ClearDb
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * entities
     *
     * @var mixed
     */
    private $entities;

    /**
     * tables
     *
     * @var mixed
     */
    private $tables;

    /**
     * fields
     *
     * @var mixed
     */
    private $fields;

    /**
     * container
     *
     * @var mixed
     */
    protected $container = null;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(
        EntityManager $em,
        \Symfony\Component\DependencyInjection\Container $container
    )
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * setEntities
     *
     * @param mixed $entities
     *
     * @return this
     */
    public function setEntities($entities)
    {
        $this->entities = $entities;

        return $this;
    }

    /**
     * setTables
     *
     * @param mixed $tables
     */
    public function setTables($tables)
    {
        $this->tables = $tables;

        return $this;
    }

    /**
     * setNotDeletedFields
     *
     * @param mixed $fields
     *
     * @return this
     */
    public function setNotDeletedFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * clear
     *
     * @return boolean
     */
    public function clear()
    {
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();

        foreach ($this->tables as $table) {
            $truncateSql = $platform->getTruncateTableSql($table);
            $connection->executeUpdate($truncateSql);
        }

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($this->entities as $entity) {
            $this->delete($entity);
        }

        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');

        return true;
    }

    /**
     * undocumented function
     *
     * @return boolean
     */
    private function delete($entity)
    {
        $qb = $this->em->createQueryBuilder();
        $delete = $qb->delete('AppBundle:' . $entity, 'entity');
        if (isset($this->fields[$entity])) {
            $delete
                ->where($qb->expr()->notIn('entity.name', $this->fields[$entity]))
                ;
        }
        $delete
            ->getQuery()
            ->execute()
            ;
        return null;
    }

}
