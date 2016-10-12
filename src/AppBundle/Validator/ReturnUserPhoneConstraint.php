<?php

namespace AppBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class ReturnUserPhoneConstraint
 * @package AppBundle\Validator
 */
class ReturnUserPhoneConstraint extends Constraint
{
    public $message = 'Введенные данные не совпадают с данными возвращаемого заказа';
}