<?php

namespace AppBundle\Services;

use AppBundle\Entity\ProductColors;
use Illuminate\Support\Arr;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class: Filters
 * @author zimm
 */
class Filters
{

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * session
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $this->container->get('request');
    }

    /**
     * @param $data
     * @return bool
     */
    public function isShowFiltered($data)
    {
        $hasCharacteristics = false;
        foreach ($this->request->query->all() as $key => $value) {
            if (intval($key)) {
                $hasCharacteristics = true;
                break;
            }
        }
        return $hasCharacteristics || $this->request->get('price_from') && $this->request->get('price_from') != $data['price_filter']['min_price']
        || $this->request->get('price_to') && $this->request->get('price_to') != $data['price_filter']['max_price'] || $this->selectedColors($data)
        || ($this->has('shares') && !$this->container->get('security.context')->isGranted('ROLE_WHOLESALER'));
    }

    /**
     * @param $color
     * @return bool
     */
    public function isSelectedColor(ProductColors $color)
    {
        return in_array($color->getId(), Arr::get($this->parseFiltersUrl(), 'query.colors', []));
    }

    /**
     * @param $filterName
     * @param $value
     * @return bool
     */
    public function hasEq($filterName, $value)
    {
        return $this->request->get($filterName) == $value;
    }

    /**
     * @param $filterName
     * @return bool
     */
    public function has($filterName)
    {
        return $this->request->get($filterName) != false;
    }

    /**
     * @param $filterName
     * @param $default
     * @return bool
     */
    public function get($filterName, $default = false)
    {
        return $this->request->get($filterName, $default);
    }

    /**
     * @param $data
     * @return bool
     */
    public function selectedColors($data)
    {
        $data['colors'];
        $colors = array_filter($data['colors'], function (ProductColors $color) {
            return $this->isSelectedColor($color);
        });
        return $colors;
    }

    /**
     * @param $parameter
     * @param $newValue
     * @param bool|false $url
     * @return string
     */
    public function replaceQueryParameter($parameter, $newValue = false, $url = false)
    {
        $url = $this->parseFiltersUrl($url);

        if ($newValue) {
            $url['query'][$parameter] = $newValue;
        } else {
            unset($url['query'][$parameter]);
        }

        return $this->makeFiltersUrl($url);
    }

    /**
     * @param $parameter
     * @param $value
     * @param bool|false $url
     * @return string
     */
    public function removeQueryParameter($parameter, $value, $url = false)
    {
        $url = $this->parseFiltersUrl($url);

        if (isset($url['query'][$parameter]) && is_array($url['query'][$parameter])) {
            unset($url['query'][$parameter][array_search($value, $url['query'][$parameter])]);
        }

        return $this->makeFiltersUrl($url);
    }

    /**
     * @param $characteristicValueId
     * @param bool|false $url
     * @return string
     */
    public function isChecked($characteristicValueId, $url = false)
    {
        $url = $this->parseFiltersUrl($url);

        foreach ($url['query'] as $key => $value) {

        }

        return false;
    }

    /**
     * @param bool|false $url
     * @return bool|mixed|string
     */
    protected function parseFiltersUrl($url = false)
    {
        $url || $url = $this->request->getUri();
        $url = parse_url($url);

        if(!isset($url['query'])) {
            $url['query'] = '';
        }

        parse_str($url['query'], $url['query']);

        foreach ($url['query'] as $key => $value) {
            if (is_string($value)) {
                $url['query'][$key] = explode(',', $value);
            }
        }

        return $url;
    }

    /**
     * @param bool|false $url
     * @return string
     */
    protected function makeFiltersUrl($url = false)
    {
        if (isset($url['query'])) {
            foreach ($url['query'] as $key => $value) {
                if (is_array($value)) {
                    $url['query'][$key] = implode(',', $value);
                }
                if (empty($url['query'][$key])) {
                    unset($url['query'][$key]);
                }
            }
        }

        $url['query'] = http_build_query($url['query']);

        return http_build_url($url);
    }
}
