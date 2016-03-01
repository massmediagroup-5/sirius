<?php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use \Twig_Extension;

/**
 * Class: AppExtension
 *
 * @see \Twig_Extension
 */
class AppExtension extends \Twig_Extension
{
    /**
     * container
     *
     * @var mixed
     */
    protected $container;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }

    /**
     * getFilters
     *
     * @return array
     */
    public function getFilters() {
        return array(
            'json_decode'   => new \Twig_Filter_Method($this, 'jsonDecode'),
            'regroup'       => new \Twig_Filter_Method($this, 'regroup'),
        );
    }

    /**
     * jsonDecode
     *
     * @param mixed $str
     *
     * @return mixed
     */
    public function jsonDecode($str) {
        return json_decode($str);
    }

    /**
     * regroup
     *
     * @param mixed $formView
     *
     * @return FormView
     */
    public function regroup($formView)
    {
        $category = array();
        foreach ($formView->children as $itemKey => $children) {
            $item = explode(': ', $children->vars['label']);
            if (!isset($category[$item[0]])) {
                $category[$item[0]] = new FormView;
                $category[$item[0]]->vars = $formView->vars;
                $category[$item[0]]->vars['form'] = $category[$item[0]];
                $category[$item[0]]->vars['label'] = $item[0] . ':';
                $category[$item[0]]->vars['id'] = sha1(mt_rand());
                $category[$item[0]]->vars['unique_block_prefix'] = '_' . $category[$item[0]]->vars['id'];
                $category[$item[0]]->vars['cache_key'] = '_' . $category[$item[0]]->vars['id'];
                $category[$item[0]]->children = array();
            }
            $children->vars['label'] = $item[1];
            $category[$item[0]]->children[] = $children;
        }

        return $category;
    }

}
