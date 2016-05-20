<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Unisender
 *
 * @ORM\Table(name="unisender")
 * @ORM\Entity
 */
class Unisender
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
     * @ORM\Column(name="phones", type="text", length=65535, nullable=true)
     */
    private $phones;

    /**
     * @var string
     *
     * @ORM\Column(name="api_key", type="string", length=255, nullable=true)
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_name", type="string", length=255, nullable=true)
     */
    private $senderName;

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
     * Set phones
     *
     * @param string $phones
     *
     * @return Unisender
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return Unisender
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
     * Set senderName
     *
     * @param string $senderName
     *
     * @return Unisender
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * Get senderName
     *
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Unisender
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
