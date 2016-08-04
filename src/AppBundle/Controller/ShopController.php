<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


class ShopController extends Controller
{
    /**
     * @var array the breadcrumbs of the current page.
     */
    private $breadcrumb = array();

    /**
     * @var number of max items count on page.
     */
    private $items_on_page = 9;

    /**
     * @Route("/wishlist", name="wishlist")
     */
    public function wishlistAction(Request $request)
    {
        $wishlist = $request->getSession()->get('wishlist');
        $data = $this->getDoctrine()->getRepository('AppBundle:ProductModels')->findBy(
            array('active' => 1, 'id' => $wishlist),
            array('id' => 'ASC')
        );
        return $this->render('AppBundle:userpart:wishlist.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
            'data' => $data,
            'params' => $this->get('options')->getParams(),
            'cart' => $this->get('cart')->getHeaderBasketInfo(),
            'compare' => $this->get('compare')->getHeaderCompareInfo(),
            'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
        ));
    }

    /**
     * categoryAction
     *
     * @Route("/{category}/", name="category", options={"expose"=true})
     *
     * @param mixed $category
     * @param Request $request
     *
     * @return mixed
     */
    public function categoryAction($category, Request $request)
    {
        $current_page = $request->get('page') ? $request->get('page') : 1;
        $filters = $request->query->all();
        $filters['page'] = 'page';
        try {
            if ($this->container->get('security.context')->isGranted('ROLE_WHOLESALER')) {
                $entityName = 'ProductModels';
                unset($filters['shares']);
                unset($filters['share']);
            } else {
                $entityName = 'Products';
            }
            $data = $this->get('entities')
                ->getCollectionsByCategoriesAlias($category, $filters, $this->items_on_page, $current_page,
                    $entityName);
        } catch (\Doctrine\Orm\NoResultException $e) {
            $data = null;
        }

        if ($data) {
            $category_list = $this->get('entities')->getAllActiveCategoriesForMenu();
            // Add breadcrumbs
            if ($data['category']->getParrent()) {
                $this->buildBreadcrumb($category_list, $data['category']->getParrent()->getId());
            }
            $this->get('widgets.breadcrumbs')->push(['name' => $data['category']->getName()]);
            $this->get('last_urls')->setLastCatalogUrl($request->getRequestUri());
//            dump($data);exit; 
            return $this->render('AppBundle:shop:category.html.twig', array(
                'data' => $data,
                'breadcrumb' => $this->breadcrumb,
                'params' => $this->get('options')->getParams(),
                'maxPages' => ceil($data['products']->count() / $this->items_on_page),
                'thisPage' => $current_page,
                'cart' => $this->get('cart'),
            ));
        } else {
            throw $this->createNotFoundException();
        }
    }

    /**
     * filterAction
     *
     * @Route("/{category}/filter", name="filter", options={"expose"=true})
     *
     * @param mixed $category
     * @param Request $request
     *
     * @return mixed
     */
    public function filterAction($category, Request $request)
    {
        $current_page = $request->get('page') ? $request->get('page') : 1;

        $filters = $request->query->all();

        // Redirect to category if empty filters.
        if (empty($filters)) {
            return $this->redirectToRoute('category', array('category' => $category), 301);
        }
        $filters['page'] = 'page'; // Todo ask why that needed
        try {
            $entityName = $this->container->get('security.context')->isGranted('ROLE_WHOLESALER') ? 'ProductModels' : 'Products';
            $data = $this->get('entities')
                ->getCollectionsByCategoriesAlias($category, $filters, $this->items_on_page, $current_page,
                    $entityName);
        } catch (\Doctrine\Orm\NoResultException $e) {
            $data = null;
        }
        if ($data) {
            $category_list = $this->get('entities')->getAllActiveCategoriesForMenu();
            // Add breadcrumbs
            if ($data['category']->getParrent()) {
                $this->buildBreadcrumb($category_list, $data['category']->getParrent()->getId());
            }
            $this->get('widgets.breadcrumbs')->push(['name' => $data['category']->getName()]);

            return $this->render('AppBundle:shop:category.html.twig', array(
                'data' => $data,
                'breadcrumb' => $this->breadcrumb,
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
     * buildBreadcrumb
     *
     * @param mixed $category_list
     * @param mixed $current_cat_id
     */
    private function buildBreadcrumb($category_list, $current_cat_id)
    {
        foreach ($category_list as $cat) {
            if ($current_cat_id == $cat['id']) {
                $this->get('widgets.breadcrumbs')->push([
                    'url' => $this->get('router')->generate('category', array(
                        'category' => $cat['alias']
                    )),
                    'name' => $cat['name']
                ]);
                if ($cat['parrent']['id'] != 1) {
                    $this->buildBreadcrumb($category_list, $cat['parrent']['id']);
                }
            }
        }
    }
}
