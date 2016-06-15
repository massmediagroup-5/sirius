<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Novaposhta
 *
 * @ORM\Table(name="novaposhta")
 * @ORM\Entity
 */
class Novaposhta
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=255, nullable=true)
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="warehouse_ref", type="string", length=255, nullable=true)
     */
    private $warehouseRef;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return Novaposhta
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get apiKey
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set warehouseRef
     *
     * @param string $warehouseRef
     *
     * @return Novaposhta
     */
    public function setWarehouseRef($warehouseRef)
    {
        $this->warehouseRef = $warehouseRef;

        return $this;
    }

    /**
     * Get warehouseRef
     *
     * @return string
     */
    public function getWarehouseRef()
    {
        return $this->warehouseRef;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Novaposhta
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Novaposhta
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
