<?php

namespace AppBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class ProductPriceConstraint
 * @package AppBundle\Validator
 */
class ProductPriceConstraint extends Constraint
{
    public $message = 'Нельзя создать товар с нулевой ценой';
}