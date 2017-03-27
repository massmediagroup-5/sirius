<?php

namespace AppBundle\Validator;

use Symfony\Component\PropertyAccess\PropertyAccess;
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
        $rootDataObject = $this->context->getRoot()->getNormData();
        $accessor = PropertyAccess::createPropertyAccessor();
        if (!$value && !$accessor->getValue($rootDataObject, $constraint->parentPriceField)) {
            $this->context->addViolation($constraint->message);

            return false;
        }
        return true;
    }
}