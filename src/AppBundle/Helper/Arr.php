<?php

namespace AppBundle\Helper;

/**
 * Class ArrayUtils
 * @package AppBundle\Services
 */
class Arr extends \Illuminate\Support\Arr
{
    public static function sumProperty($array, $property)
    {
        $propertyGetter = 'get' . ucfirst($property);

        return array_sum(array_map(function ($item) use ($propertyGetter) {
            return $item->$propertyGetter();
        }, $array));
    }
}
