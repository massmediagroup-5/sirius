<?php

namespace AppBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class ReturnOrderNumConstraint
 * @package AppBundle\Validator
 */
class ReturnOrderNumConstraint extends Constraint
{
    public $message = 'Введенные данные не совпадают с данными возвращаемого заказа';
}