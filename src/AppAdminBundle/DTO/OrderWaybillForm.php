<?php

namespace AppAdminBundle\DTO;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OrderWaybillForm
 *
 * @package AppAdminBundle\Block
 */
class OrderWaybillForm
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $np_surname;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $np_name;

    /**
     * @var string
     */
    public $np_middlename;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $np_phone;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $np_cost;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $np_delivery_payer;

    /**
     * @var
     */
    public $np_backward_delivery_cost;

    /**
     * @var string
     * @Assert\Expression(
     *     "this.np_backward_delivery_cost ? this.np_backward_delivery_payer : true",
     *     message="Заполните информацию о обратной доставке полностью"
     * )
     */
    public $np_backward_delivery_payer;
}