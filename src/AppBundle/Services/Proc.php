<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class: Proc
 * @author linux0uid
 *
 */
class Proc
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * rootDir
     *
     * @var mixed
     */
    private $rootDir;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container, $root)
    {
        $this->em = $em;
        $this->container = $container;
        $this->rootDir = $root;
    }

    /**
     * runUpdateRelationship
     *
     * @param string $relationship
     */
    public function runUpdateRelationship($relationship = "CategoriesAndProducts")
    {
        $command = $this->rootDir . '/console ';
        $command .= 'update:relationship ' . $relationship;

        //$command = "sleep 25";
        $process = new Process($command . ' &');
        $process->disableOutput();
        $process->run();
        
        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

}
