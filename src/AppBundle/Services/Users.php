<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class: Users
 * @author A.Kravchuk
 */
class Users
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;


    /**
     * __construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * getCurrentUser
     *
     * @return mixed
     *
     * Return current User object
     */
    public function getCurrentUser()
    {

        $user = $this->em->getRepository('AppBundle:Users')
            ->findOneById(1);

        return $user;
    }

}
