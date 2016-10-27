<?php

namespace AppBundle\HistoryItem;


use AppBundle\Entity\History;
use AppBundle\Entity\OrderHistory;
use AppBundle\Entity\ProductModelsHistory;
use AppBundle\Entity\ReturnedSizesHistory;
use AppBundle\Entity\ReturnProduct;
use AppBundle\Entity\ReturnProductHistory;
use Doctrine\ORM\EntityManager;
use Illuminate\Support\Str;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\DataCollectorTranslator;

/**
 * Class AbstractHistoryItem
 * @package AppBundle\HistoryItem
 * Todo remove $historyPrefix and get meta information using reflection.
 * Todo And create "HistoryItemDecoratorInterface" for specific Entity behavior
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
     * @var string
     */
    protected $historyPrefix;

    /**
     * AbstractHistoryItem constructor.
     * @param ContainerInterface $container
     * @param History $history
     */
    public function __construct(ContainerInterface $container, History $history = null, $historyPrefix = null)
    {
        $this->history = $history;
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->translator = $container->get('translator');
        $this->historyPrefix = $historyPrefix;
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

    protected function getPrefixForLabel(){

        if($this->history instanceof ReturnProductHistory){
            return 'return_product';
        }
        if($this->history instanceof ReturnedSizesHistory){
            return 'return_product_size';
        }
        if($this->history instanceof OrderHistory){
            return 'order';
        }
        if($this->history instanceof ProductModelsHistory){
            return 'product_models';
        }
        return Str::snake((new \ReflectionClass($this->history))->getShortName());
    }

    protected function getPrefixForRollBack(){

        $reflect = new \ReflectionClass($this->history);
        return $reflect->getShortName();
    }
}
