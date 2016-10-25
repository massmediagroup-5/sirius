<?php

namespace AppBundle\Validator;


use AppBundle\Entity\OrderStatus;
use AppBundle\Entity\ReturnProduct;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ReturnUserPhoneConstraintValidator
 * @package AppBundle\Validator
 */
class ReturnUserPhoneConstraintValidator extends ConstraintValidator
{
    private $em;

    /**
     * ReturnUserPhoneConstraintValidator constructor.
     */
    public function __construct(EntityManager $entityManager){

        $this->em = $entityManager;
    }

    /**
     * @param ReturnProduct $value
     * @param Constraint $constraint
     * @return bool
     */
    public function validate($value, Constraint $constraint)
    {
        if($this->em->getRepository('AppBundle:Users')->findOneByPhone($value))
            {
            return true;
        }else{
            $this->context->addViolation($constraint->message);
            return false;
        }
    }
}