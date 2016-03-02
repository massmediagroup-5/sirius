<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class: SearchController
 *
 * @see Controller
 */
class SearchController extends Controller
{

    /**
     * searchAction
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function formAction(Request $request)
    {

        $data = array();
        $form = $this->createFormBuilder($data)
            ->setAction($this->generateUrl('form'))
            ->add('search', 'text', array('label' => false))
            ->add('save', 'submit')
            ->getForm();
        //dump($form, $request);
        if ($request->isMethod('POST')) {
            $form->submit($request);
            //dump($form->getData(), $request);
            if ($form->isValid()) {
                $search = base64_encode($form->getData()['search']);
                //dump($search);
                return $this->redirectToRoute(
                    'search',
                    array('search' => urlencode($search))
                );
            }
        }
        return $this->render('AppBundle:Search:form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * searchAction
     *
     * @param mixed $search
     *
     * @return mixed
     */
    public function searchAction($search)
    {
        $slug = base64_decode(urldecode($search));
        $finder = $this->get('fos_elastica.finder.app.products');

        $boolQuery = new \Elastica\Query\BoolQuery();

        $fieldQuery = new \Elastica\Query\Match();
        $fieldQuery->setFieldQuery('title', 'I am a title string');
        $fieldQuery->setFieldParam('title', 'analyzer', 'my_analyzer');
        $boolQuery->addShould($fieldQuery);

        $tagsQuery = new \Elastica\Query\Terms();
        $tagsQuery->setTerms('tags', array('tag1', 'tag2'));
        $boolQuery->addShould($tagsQuery);

        $categoryQuery = new \Elastica\Query\Terms();
        $categoryQuery->setTerms('categoryIds', array('1', '2', '3'));
        $boolQuery->addMust($categoryQuery);


        $result = $finder->find($slug);
//        $result = $this->get('search')->doSearch($slug);
//        var_dump($result);exit;
        return $this->render('AppBundle:Search:search.html.twig', array(
            'data'              => $result,
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'params'            => $this->get('options')->getParams(),
            'cart'              => $this->get('cart')->getHeaderBasketInfo(),
            'compare'           => $this->get('compare')->getHeaderCompareInfo(),
            'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
        ));
    }

}
