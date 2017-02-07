<?php

namespace AppBundle\Factory;


use AppBundle\Entity\Users;
use AppBundle\Services\PricesCalculator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Class PricesCalculatorFactory
 * @package AppBundle\Factory
 * @author R. Slobodzian
 */
class PricesCalculatorFactory
{
    /**
     * @param ContainerInterface $container
     * @param EntityManager $em
     * @param TokenStorage $tokenStorage
     * @return PricesCalculator
     */
    public function createPricesCalculator(
        ContainerInterface $container,
        EntityManager $em,
        TokenStorage $tokenStorage
    ) {
        $user = $tokenStorage->getToken() ? $tokenStorage->getToken()->getUser() : null;

        if (!is_object($user)) {
            $user = new Users();
        }

        return new PricesCalculator($container, $em, $user);
    }
}
