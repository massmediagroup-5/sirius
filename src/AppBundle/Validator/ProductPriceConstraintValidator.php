<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ProductPriceConstraintValidator
 * @package AppBundle\Validator
 */
class ProductPriceConstraintValidator extends ConstraintValidator
{
    /**
     * @param ProductPrice $value
     * @param Constraint $constraint
     * @return bool
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value && !$this->context->getRoot()->getNormData()->getPrice()) {
            $this->context->addViolation($constraint->message);

            return false;
        }
        return true;
    }
}