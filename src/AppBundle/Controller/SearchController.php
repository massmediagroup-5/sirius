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
     * @var number of max items count on page.
     */
    private $items_on_page = 9;

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
     * @param Request $request
     *
     * @return mixed
     */
    public function searchAction($search = null, Request $request)
    {
        if(!$search){
            return $this->redirectToRoute('search', array('search' => $request->get('search')), 301);
        }
        $slug = urldecode($search);

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
        $client = new \Elastica\Client();

        $resultSet = $client->getIndex('app')->search($query);

        $ids = array_map(function ($res) {
            return $res->getId();
        }, $resultSet->getResults());

        if (!$ids) {
            return $this->render('AppBundle:shop:search.html.twig', ['data' => false, 'slug' => $slug]);
        }

        $category = 'all';
        $current_page = $request->get('page') ? $request->get('page') : 1;
        $filters = $request->query->all();

        try {
            $entityName = $this->container->get('security.context')->isGranted('ROLE_WHOLESALER') ? 'ProductModels' : 'Products';
            $data = $this->get('entities')
                ->getCollectionsByCategoriesAlias($category, $filters, $this->items_on_page, $current_page,
                    $entityName, $ids);
        } catch (\Doctrine\Orm\NoResultException $e) {
            $data = null;
        }
        if ($data) {
            $this->get('widgets.breadcrumbs')->push(['name' => 'Результаты поиска']);
            return $this->render('AppBundle:shop:search.html.twig', array(
                'data' => $data,
                'slug' => $slug,
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
                'params' => $this->get('options')->getParams(),
                'maxPages' => ceil($data['products']->count() / $this->items_on_page),
                'thisPage' => $current_page
            ));
        } else {
            throw $this->createNotFoundException();
        }
    }

}
