<?php

namespace AppBundle\FileReader\Transformer;

/**
 * Interface TransformerInterface
 * @package AppBundle\Interfaces
 * @author zimm
 */
interface TransformerInterface
{

    /**
     * Get record located in given index
     *
     * @param $value
     * @return array converted record
     */
    public function transform($value);

}
