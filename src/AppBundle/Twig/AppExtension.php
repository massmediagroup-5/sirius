<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Categories;
use AppBundle\Entity\ProductModels;
use Illuminate\Support\Arr;
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
            'format_price'       => new \Twig_Filter_Method($this, 'formatPrice'),
        );
    }

    /**
     * getFilters
     *
     * @return array
     */
    public function getFunctions() {
        return array(
            'widget'       => new \Twig_Function_Method($this, 'widget'),
            'param'       => new \Twig_Function_Method($this, 'param'),
            'url_to'       => new \Twig_Function_Method($this, 'urlTo'),
            'add_error'       => new \Twig_Function_Method($this, 'addError'),
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
     * @param $number
     * @return string
     */
    public function formatPrice($number) {
        return number_format($number, 2, '.', '');
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

    /**
     * Render widget
     *
     * @return mixed
     */
    public function widget() {
        $args = func_get_args();

        $widgetNameAndMethod = Arr::get($args, 0);

        if(!is_string($widgetNameAndMethod) || strpos($widgetNameAndMethod, '.') === false) {
            throw new \InvalidArgumentException('First parameter must be string with widget name and method. Example "posts.last".');
        } else {
            list($widgetName, $widgetMethodName) = explode('.', $widgetNameAndMethod);
        }

        // Add prefix to widget name
        $widgetName = "widgets.$widgetName";

        // Get parameters
        $widgetParameters = array_slice($args, 1);

        $widgetObject = $this->container->get($widgetName);

        return call_user_func_array([$widgetObject, $widgetMethodName], $widgetParameters);
    }

    /**
     * Get config parameter
     *
     * @param string $parameter
     * @return mixed
     */
    public function param($parameter) {
        return $this->container->getParameter($parameter);
    }

    /**
     * Generate link to given object
     *
     * @return mixed
     */
    public function urlTo() {
        $args = func_get_args();
        foreach ($args as $key => $arg) {
            $key = "model" . ($key + 1);
            $$key = $arg;
        }
        $router = $this->container->get('router');

        if (isset($model1)) {
            if ($model1 instanceof Categories) {
                if (isset($model2)) {
                    if ($model2 instanceof ProductModels) {
                        return $router->generate('product', array(
                            'category' => $model1->getAlias(),
                            'product' => $model2->getAlias()
                        ));
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param FormView $form
     * @param $fields
     * @param string $class
     * @return bool
     */
    public function addError(FormView $form, $fields, $class = 'inp_error') {
        $fields = (array)$fields;
        foreach($fields as $field) {
            if($form->children[$field]->vars['errors']->count()) {
                return $class;
            }
        }
        return '';
    }

}
