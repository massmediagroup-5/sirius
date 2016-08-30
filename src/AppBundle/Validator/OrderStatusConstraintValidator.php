<?php

namespace AppBundle\Validator;


use AppBundle\Entity\OrderStatus;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class OrderStatusConstraintValidator
 * @package AppBundle\Validator
 */
class OrderStatusConstraintValidator extends ConstraintValidator
{
    /**
     * @param OrderStatus $value
     * @param Constraint $constraint
     * @return bool
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value->getCode() == 'accepted') {
            foreach ($this->context->getRoot()->getData()->getSizes() as $size) {
                if (!$size->hasValidQuantity()) {
                    $this->context->addViolation($constraint->message);

                    return false;
                }
            }
        }

        return true;
    }
}