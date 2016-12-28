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
     * @Assert\NotBlank
     */
    public $np_middlename;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $np_phone;
}