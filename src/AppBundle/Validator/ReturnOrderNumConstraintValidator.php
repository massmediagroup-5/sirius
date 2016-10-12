<?php

namespace AppBundle\Validator;


use AppBundle\Entity\OrderStatus;
use AppBundle\Entity\ReturnProduct;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ReturnOrderNumConstraintValidator
 * @package AppBundle\Validator
 */
class ReturnOrderNumConstraintValidator extends ConstraintValidator
{
    private $em;

    /**
     * ReturnOrderNumConstraintValidator constructor.
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
        if($this->em->getRepository('AppBundle:Orders')->find($value)){
            return true;
        }else{
            $this->context->addViolation($constraint->message);
            return false;
        }
    }
}