<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class CompareController extends Controller
{

    /**
     * compareCategoriesAction
     *
     * @Route("/compare-categories", name="compare-categories", options={"expose"=true})
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function compareCategoriesAction(Request $request)
    {
        $compare = $this->get('compare')->getHeaderCompareInfo();
        if($compare['compare_qty'] == 0){
            return $this->redirectToRoute('homepage');
        }
        foreach($compare['compare_ids'] as $category_id => $model_ids){
            $models = $this->getDoctrine()->getRepository('AppBundle:ProductModels')->findBy(
                array('active'=>1,'published'=>1,'id'=>array_keys($model_ids['models'])),
                array('id'=>'ASC')
            );
            $compare['categories'][$compare['compare_ids'][$category_id]['date']]['models'] = $models;
        }
        return $this->render('AppBundle:userpart:compare_categories.html.twig', array(
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'params'            => $this->get('options')->getParams(),
            'cart'              => $this->get('cart')->getHeaderBasketInfo(),
            'compare'           => $compare,
            'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
        ));
    }

    /**
     * compareAction
     *
     * @Route("/compare-selected/{id}", name="compare-selected")
     *
     * @param mixed $id
     * @param Request $request
     *
     * @return mixed
     */
    public function compareAction($id = null, Request $request)
    {
        $compare = $this->get('compare')->getHeaderCompareInfo();
        if( empty($compare['compare_ids'][$id]) ){
            return $this->redirectToRoute('compare-categories');
        }
        $current_category = $compare['categories'][$compare['compare_ids'][$id]['date']];
        foreach($compare['compare_ids'][$id]['models'] as $model){
            $result = $this->getDoctrine()->getRepository('AppBundle:Products')
                ->getProductInfoById($model['model_id']);
            if($result){
                $current_category['models'][$model['date']]['model'] = $result['productModels'][0];
                foreach($result['characteristicValues'] as $value){
                    $char_val[$value['characteristics']['id']]['id']= $value['characteristics']['id'];
                    $char_val[$value['characteristics']['id']]['name']= $value['characteristics']['name'];
                    $char_val[$value['characteristics']['id']]['value']= $value['name'];
                    $char_val[$value['characteristics']['id']]['class']= ' equal';
                }
                ksort($char_val);
                $current_category['models'][$model['date']]['char_val'] = $char_val;
            }
        }
        krsort($current_category['models']);
        foreach($current_category['models'] as $model){
            foreach($model['char_val'] as $char_val){
                $array_characteristics[$char_val['id']]['id'] = $char_val['id'];
                $array_characteristics[$char_val['id']]['name'] = $char_val['name'];
                $array_characteristics[$char_val['id']]['value'] = 'Нет данных';
                $array_characteristics[$char_val['id']]['class'] = ' equal';
            }
        }
        ksort($array_characteristics);
        foreach($current_category['models'] as $key => $model){
            $char_val = $model['char_val'] + $array_characteristics;
            ksort($char_val);
            $current_category['models'][$key]['char_val'] = $char_val;
        }
        foreach($current_category['models'] as $first_key => $model){
            foreach($current_category['models'] as $current){
                foreach($current['char_val'] as $key => $value){
                    if($model['char_val'][$key]['value'] != $value['value']){
                        $current_category['models'][$first_key]['char_val'][$key]['class'] = ' different';
                        $array_characteristics[$key]['class'] = ' different';
                    }
                }
            }
        }
        return $this->render('AppBundle:userpart:compare_selected.html.twig', array(
            'base_dir'              => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'params'                => $this->get('options')->getParams(),
            'cart'                  => $this->get('cart')->getHeaderBasketInfo(),
            'compare'               => $this->get('compare')->getHeaderCompareInfo(),
            'curent_category'       => $current_category,
            'array_characteristics' => $array_characteristics,
            'recently_reviewed'     => $this->get('entities')->getRecentlyViewed(),
        ));
    }
}
