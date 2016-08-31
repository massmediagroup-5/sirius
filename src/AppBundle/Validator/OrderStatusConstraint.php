<?php

namespace AppBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class OrderStatusConstraint
 * @package AppBundle\Validator
 */
class OrderStatusConstraint extends Constraint
{
    public $message = 'Нельзя поменять статус если товаров выбрано больше чем в наличии';
}