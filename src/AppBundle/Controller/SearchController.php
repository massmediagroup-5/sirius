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
            return $this->redirectToRoute('search', array('search' => urlencode($request->get('search'))), 301);
        }
        $slug = urldecode($search);

        $boolQuery = new \Elastica\Query\BoolQuery();

        $productQuery = new \Elastica\Query\Match();
        $productQuery->setFieldQuery('name', $slug);
        $productQuery->setFieldParam('name', 'boost', 3);
        $productQuery->setFieldParam('name', 'type', 'phrase_prefix');
        $boolQuery->addShould($productQuery);

        $articleQuery = new \Elastica\Query\Match();
        $articleQuery->setFieldQuery('article', $slug);
        $articleQuery->setFieldParam('article', 'boost', 3);
        $articleQuery->setFieldParam('article', 'type', 'phrase_prefix');
        $boolQuery->addShould($articleQuery);

        $contentQuery = new \Elastica\Query\Match();
        $contentQuery->setFieldQuery('content', $slug);
        $contentQuery->setFieldParam('content', 'boost', 3);
        $contentQuery->setFieldParam('content', 'type', 'phrase_prefix');
        $boolQuery->addShould($contentQuery);

        $characteristicsQuery = new \Elastica\Query\Match();
        $characteristicsQuery->setFieldQuery('characteristics', $slug);
        $characteristicsQuery->setFieldParam('characteristics', 'boost', 3);
        $characteristicsQuery->setFieldParam('characteristics', 'type', 'phrase_prefix');
        $boolQuery->addShould($characteristicsQuery);

        $featuresQuery = new \Elastica\Query\Match();
        $featuresQuery->setFieldQuery('features', $slug);
        $featuresQuery->setFieldParam('features', 'boost', 3);
        $featuresQuery->setFieldParam('features', 'type', 'phrase_prefix');
        $boolQuery->addShould($featuresQuery);

        $baseCategoryQuery = new \Elastica\Query\Match();
        $baseCategoryQuery->setFieldQuery('baseCategory.name', $slug);
        $baseCategoryQuery->setFieldParam('baseCategory.name', 'boost', 3);
        $baseCategoryQuery->setFieldParam('baseCategory.name', 'type', 'phrase_prefix');
        $boolQuery->addShould($baseCategoryQuery);

        $productColorsQuery = new \Elastica\Query\Match();
        $productColorsQuery->setFieldQuery('productModels.productColors.name', $slug);
        $productColorsQuery->setFieldParam('productModels.productColors.name', 'boost', 3);
        $productColorsQuery->setFieldParam('productModels.productColors.name', 'type', 'phrase_prefix');
        $boolQuery->addShould($productColorsQuery);

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
