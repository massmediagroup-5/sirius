<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\History;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * Class AbstractHistoryItem
 * @package AppBundle\HistoryItem
 */
abstract class AbstractHistoryItem
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var History
     */
    protected $history;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var DataCollectorTranslator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $nameRepository;

    /**
     * AbstractHistoryItem constructor.
     * @param ContainerInterface $container
     * @param History $history
     */
    public function __construct(ContainerInterface $container, History $history = null)
    {
        $this->history = $history;
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->translator = $container->get('translator');
    }

    /**
     * @return mixed
     */
    abstract public function label();

    /**
     * @return mixed
     */
    public function rollback()
    {
        if ($this->canRollback()) {
            $this->makeRollback();
            return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function canRollback()
    {
        // Can rollback only when not have newest updates with same types
        return $this->em->getRepository($this->getNameRepository())->countOfNewest($this->history) == 0;
    }

    /**
     * @return History
     */
    public function getEntity()
    {
        return $this->history;
    }

    /**
     * @return mixed
     */
    abstract protected function makeRollback();

    /**
     * @return string
     */
    public function getNameRepository()
    {
        $this->nameRepository = get_class();
        return $this->nameRepository;
    }

}
