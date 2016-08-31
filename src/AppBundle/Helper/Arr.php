<?php

namespace AppBundle\Helper;

/**
 * Class ArrayUtils
 * @package AppBundle\Services
 */
class Arr extends \Illuminate\Support\Arr
{
    /**
     * @param $array
     * @param $property
     * @return number
     */
    public static function sumProperty($array, $property)
    {
        return array_sum(self::mapProperty($array, $property));
    }

    /**
     * @param $array
     * @param $property
     * @return array
     */
    public static function mapProperty($array, $property)
    {
        $propertyGetter = 'get' . ucfirst($property);

        return array_map(function ($item) use ($propertyGetter) {
            return $item->$propertyGetter();
        }, $array);
    }
}
