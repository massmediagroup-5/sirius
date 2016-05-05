<?php

namespace AppAdminBundle\Controller;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Class: CRUDController
 *
 * @see BaseController
 */
class OrderController extends BaseController
{
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function moveSizeAction(Request $request)
    {
        $object = $this->admin->getSubject();

        $size = $this->getDoctrine()
            ->getManager()
            ->getRepository('AppBundle:OrderProductSize')
            ->find($request->get('size'));

        $this->get('order')->moveSize($object, $size, $request->get('quantity'));

        return $this->renderJson($this->renderPartials());
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function removeSizeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->get('order')->removeSize(
            $this->admin->getSubject(),
            $em->getRepository('AppBundle:OrderProductSize')->find($request->get('size'))
        );

        return $this->renderJson($this->renderPartials());
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function addSizesAction(Request $request)
    {
        $object = $this->admin->getSubject();

        $sizes = [];
        foreach ($request->get('sizes') as $sizeArray) {
            $sizes[] = [
                $this->getDoctrine()
                    ->getManager()
                    ->getRepository('AppBundle:ProductModelSpecificSize')
                    ->find($sizeArray['id']),
                $sizeArray['count']
            ];
        }

        $this->get('order')->addSizes($object, $sizes);

        return $this->renderJson($this->renderPartials());
    }

    /**
     * @return RedirectResponse
     */
    public function changePreOrderFlagAction()
    {
        $object = $this->admin->getSubject();

        $object = $this->get('order')->changePreOrderFlag($object);

        return $this->redirectTo($object);;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxUpdateAction(Request $request)
    {
        $data = Arr::only($request->request->all(), [
            'individualDiscount',
            'additionalSolarDescription',
            'additionalSolar',
        ]);
        $object = $this->admin->getSubject();

        $serializer = new Serializer([new ObjectNormalizer]);

        $object = $serializer->denormalize($data, 'AppBundle\\Entity\\Orders', null,
            ['object_to_populate' => $object]);

        $this->getDoctrine()->getManager()->persist($object);
        $this->getDoctrine()->getManager()->flush();

        return $this->renderJson([
            'history' => $this->renderView('AppAdminBundle:admin:order_history_items.html.twig', [
                'admin' => $this->admin
            ])
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSizesAction(Request $request)
    {
        $admin = $this->admin;

        $filters = $request->request->all();

        $models = $this->admin->paginateModels($filters);

        $categories = $this->getDoctrine()->getRepository('AppBundle:Categories')->findAll();

        return $this->renderJson([
            'sizes' => $this->renderView('AppAdminBundle:admin:order_sizes_select.html.twig',
                compact('models', 'categories', 'filters', 'admin'))
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderPartials()
    {
        $parameters = [
            'admin' => isset($parameters['admin']) ? $parameters['admin'] : $this->admin,
            'form_tab' => [
                'name' => 'Список заказанных товаров'
            ]
        ];

        return [
            'partial' => $this->renderView('AppAdminBundle:admin:order_sizes.html.twig', $parameters),
            'history' => $this->renderView('AppAdminBundle:admin:order_history_items.html.twig', [
                'admin' => $this->admin
            ])
        ];
    }
}
