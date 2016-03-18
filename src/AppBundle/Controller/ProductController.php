<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends Controller
{

    /**
     * productAction
     *
     * @Route("/{category}/{product}", name="product", options={"expose"=true})
     *
     * @param mixed $product
     * @param Request $request
     * @return mixed
     */
    public function productAction($product, Request $request)
    {
        try {
            $result = $this->get('entities')->getProductInfoByAlias($product);
        } catch (\Doctrine\Orm\NoResultException $e) {
            $result = null;
        }
        if ($result) {
            $this->get('entities')->setRecentlyViewed($result['product']['productModels'][0]['id']);
            $category_list = $this->get('entities')->getAllActiveCategoriesForMenu();
            if(isset($result['product']['baseCategory']['id'])) {
                $this->buildBreadcrumb($category_list, $result['product']['baseCategory']['id']);
            }
            $this->get('widgets.breadcrumbs')->push(['name' => $result['product']['productModels'][0]['name']]);

            return $this->render('AppBundle:shop:product/show.html.twig', array(
                'product' => $result['product'],
                'current_model' => $result['product']['productModels'][0],
                'models' => $result['models'],
                'params' => $this->get('options')->getParams(),
                'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
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
