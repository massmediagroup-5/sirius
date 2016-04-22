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
        $slug = urldecode($search);
        $finder = $this->get('fos_elastica.finder.app');

        $boolQuery = new \Elastica\Query\BoolQuery();

        $productQuery = new \Elastica\Query\Match();
        $productQuery->setFieldQuery('products.name', $slug);
        $productQuery->setFieldParam('products.name', 'boost', 3);
        $productQuery->setFieldParam('products.name', 'type', 'phrase_prefix');
        $boolQuery->addShould($productQuery);

        $articleQuery = new \Elastica\Query\Match();
        $articleQuery->setFieldQuery('products.article', $slug);
        $articleQuery->setFieldParam('products.article', 'boost', 3);
        $articleQuery->setFieldParam('products.article', 'type', 'phrase_prefix');
        $boolQuery->addShould($articleQuery);

        $baseCategoryQuery = new \Elastica\Query\Match();
        $baseCategoryQuery->setFieldQuery('products.baseCategory.name', $slug);
        $baseCategoryQuery->setFieldParam('products.baseCategory.name', 'boost', 3);
        $baseCategoryQuery->setFieldParam('products.baseCategory.name', 'type', 'phrase_prefix');
        $boolQuery->addShould($baseCategoryQuery);

        $boolFilter = new \Elastica\Filter\Bool();

        $boolFilter->addMust(
            new \Elastica\Filter\Terms('active', array(1))
        );

        $boolFilter->addMust(
            new \Elastica\Filter\Terms('productModels.published', array(1))
        );
        $filtered = new \Elastica\Query\Filtered($boolQuery, $boolFilter);
        $query = \Elastica\Query::create($filtered);
        $result = $finder->find($query,9999,array());
        dump($result);exit;
        return $this->render('AppBundle:Search:search.html.twig', array(
            'data'              => $result,
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..')
        ));
    }

}
