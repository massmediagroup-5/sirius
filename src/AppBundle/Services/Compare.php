<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: Compare
 * @author A.Kravchuk
 */
class Compare
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * templating
     *
     * @var mixed
     */
    private $templating;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * session
     *
     * @var mixed
     */
    private $session;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container, Session $session)
    {
        $this->em = $em;
        $this->container = $container;
        $this->session = $session;
        $this->templating = $container->get('templating');
    }

    /**
     * addToCompare
     *
     * @param integer $category_id
     * @param integer $model_id
     *
     */
    public function addToCompare($category_id, $model_id)
    {
        $compare = $this->session->get('compare');
        if(!$compare) $compare = array();

        $compare[$category_id]['models'][$model_id] = array(
            'model_id'=>$model_id,
            'date'=> time()
        );
        $compare[$category_id]['date'] = time();
        $this->session->set('compare', $compare);
    }

    /**
     * removeFromCompare
     *
     * @param integer $category_id
     * @param integer $model_id
     *
     */
    public function removeFromCompare($category_id, $model_id)
    {
        $compare = $this->session->get('compare');
        if(isset($compare[$category_id]['models'][$model_id])){
            if(count($compare[$category_id]['models']) > 1){
                unset($compare[$category_id]['models'][$model_id]);
            }else{
                unset($compare[$category_id]);
            }
        }
        $this->session->set('compare', $compare);
    }

    /**
     * removeCategoryFromCompare
     *
     * @param integer $category_id
     *
     */
    public function removeCategoryFromCompare($category_id)
    {
        $compare = $this->session->get('compare');
        if(isset($compare[$category_id])){
            unset($compare[$category_id]);
        }
        $this->session->set('compare', $compare);
    }

    /**
     * getHeaderCompareInfo
     *
     * @return mixed
     *
     * Return array with Compare information
     */
    public function getHeaderCompareInfo()
    {
        $compare = $this->session->get('compare');
        $result = array(
            'compare_header_tpl'=> '',
            'compare_qty'       => 0,
            'compare_ids'       => array()
        );
        if($compare){
            foreach($compare as $count_cat){
                $result['compare_qty'] += count($count_cat['models']);
            }
            $result['compare_ids'] = $compare;
            $categories_ids = array_keys($compare);
            $categories = $this->em->getRepository('AppBundle:Categories')->findBy(
                array('active'=>1,'id'=>$categories_ids),
                array('id'=>'ASC')
            );
            foreach($categories as $category){
                $result['categories'][$compare[$category->getId()]['date']] = array(
                    'id'        => $category->getId(),
                    'name'      => $category->getName(),
                    'alias'     => $category->getAlias(),
                    'quantity'  => count($compare[$category->getId()]['models'])
                );
            }
            krsort($result['categories']);
        }
        $result['compare_header_tpl'] = $this->templating->render(
            'AppBundle:userpart:header_compare_block.html.twig',
            array('compare' => $result)
        );
        return $result;
    }
}
