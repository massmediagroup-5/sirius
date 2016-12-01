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
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $search = base64_encode($form->getData()['search']);
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
        $subSlug = $this->searchTransformer($slug);

        $category = 'all';
        $current_page = $request->get('page') ? $request->get('page') : 1;
        $filters = $request->query->all();
        $filters['search'] = $slug;
        $filters['sub_search'] = $subSlug;

        try {
            $entityName = $this->container->get('security.context')->isGranted('ROLE_WHOLESALER') ? 'ProductModels' : 'Products';
            $data = $this->get('entities')
                ->getCollectionsByCategoriesAlias($category, $filters, $this->items_on_page, $current_page,
                    $entityName);
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

    /**
     * @param $search
     * @return string
     */
    private function searchTransformer($search){

        if (mb_strlen($search) == 4){
            return mb_substr($search, 0, -1);
        }
        if (mb_strlen($search) > 4 ){
            return mb_substr($search, 0, -2);
        }
        return $search;
    }
}
