<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\CreateOrder;
use AppBundle\Form\Type\CreateOrderType;
use AppBundle\Form\Type\QuickOrderType;
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
            throw $this->createNotFoundException();
        }

        $this->get('entities')->setRecentlyViewed($result['product']->getProductModels()[0]->getId());

        $category_list = $this->get('entities')->getAllActiveCategoriesForMenu();
        if ($result['product']->getBaseCategory()) {
            $this->buildBreadcrumb($category_list, $result['product']->getBaseCategory()->getId());
        }
        $this->get('widgets.breadcrumbs')->push(['name' => $result['product']->getProductModels()[0]->getName()]);

        $form = $this->createForm(CreateOrderType::class, null, [
            'model' => $result['product']->getProductModels()[0]
        ])->createView();

        // todo process forms

        $quickForm = $this->createForm(QuickOrderType::class)->createView();

        return $this->render('AppBundle:shop:product/show.html.twig', [
            'product' => $result['product'],
            'current_model' => $result['product']->getProductModels()[0],
            'models' => $result['models'],
            'form' => $form,
            'quickForm' => $quickForm
        ]);
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
