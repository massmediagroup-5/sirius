<?php

namespace AppBundle\Entity;

/**
 * CharacteristicableInterface
 */
interface CharacteristicableInterface
{

    /**
     * Add characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     */
    public function addCharacteristicValue(\AppBundle\Entity\CharacteristicValues $characteristicValue);

    /**
     * Remove characteristicValue
     *
     * @param \AppBundle\Entity\CharacteristicValues $characteristicValue
     */
    public function removeCharacteristicValue(\AppBundle\Entity\CharacteristicValues $characteristicValue);

    /**
     * Get characteristicValues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacteristicValues();

}
