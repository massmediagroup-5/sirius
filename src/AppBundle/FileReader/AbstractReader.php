<?php

namespace AppBundle\FileReader;

use AppBundle\FileReader\Transformer\TransformerInterface;

/**
 * AbstractReader
 *
 * @author zimm
 */
abstract class AbstractReader implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Position need to implement Iterator
     * @var int
     */
    protected $position = 0;

    /**
     * @param string $file path to file
     * @param TransformerInterface $transformer
     */
    abstract public function __construct($file, TransformerInterface $transformer);

    /**
     * Get record located in given index
     *
     * @param $index
     * @return array converted record
     */
    abstract public function getDataByIndex($index);

    /**
     * @inheritdoc
     */
    public function offsetExists($offset) {
        return $offset >= 0 && $offset < $this->count();
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset) {
        return $this->getDataByIndex($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value) {
        throw new \Exception("You can not edit document.");
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset) {
        throw new \Exception("You can not edit document.");
    }

    /**
     * @inheritdoc
     */
    public function rewind() {
        $this->position = 0;
    }

    /**
     * @inheritdoc
     */
    public function current() {
        return $this[$this->position];
    }

    /**
     * @inheritdoc
     */
    public function key() {
        return $this->position;
    }

    /**
     * @inheritdoc
     */
    public function next() {
        ++$this->position;
    }

    /**
     * @inheritdoc
     */
    public function valid() {
        return $this->offsetExists($this->position);
    }

    /**
     * @param $value
     * @return float|int
     */
    protected function prepareValue($value) {
        // If string contain number
        if(is_string($value) && is_numeric($value)) {
            $value = (float)$value;
        }

        // If value is float and not decimal
        if(is_float($value) && floor( $value ) == $value) {
            $value = (int)$value;
        }
        return $value;
    }

}