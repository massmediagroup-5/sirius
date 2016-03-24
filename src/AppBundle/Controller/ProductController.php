<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Products;
use AppBundle\Form\Type\AddInCartType;
use AppBundle\Form\Type\QuickOrderType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends Controller
{

    /**
     * productAction
     *
     * @Route("/{category}/{product}", name="product", options={"expose"=true})
     * @ParamConverter("product", options={
     *      "repository_method" = "getProductInfoByAlias",
     *      "mapping": {"product": "modelAlias"},
     *      "map_method_signature" = true
     * })
     * @param mixed $product
     * @param Request $request
     * @return mixed
     */
    public function productAction(Products $product, Request $request)
    {
        $currentModel = $product->getProductModels()->first();
        $skuProduct = $currentModel->getSkuProducts()->first();
        $this->get('entities')->setRecentlyViewed($currentModel->getId());

        $category_list = $this->get('entities')->getAllActiveCategoriesForMenu();
        if ($product->getBaseCategory()) {
            $this->buildBreadcrumb($category_list, $product->getBaseCategory()->getId());
        }
        $this->get('widgets.breadcrumbs')->push(['name' => $currentModel->getName()]);

        $form = $this->createForm(AddInCartType::class, null, [
            'action' => $this->generateUrl('cart_add', ['id' => $skuProduct->getId()]),
            'model' => $currentModel
        ])->createView();

        $quickForm = $this->createForm(QuickOrderType::class, null, [
            'action' => $this->generateUrl('cart_quick_order', ['id' => $skuProduct->getId()]),
        ])->createView();

        return $this->render('AppBundle:shop:product/show.html.twig', [
            'product' => $product,
            'skuProduct' => $skuProduct,
            'current_model' => $currentModel,
            'models' => $this->get('entities')->getModelsByProduct($product),
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
