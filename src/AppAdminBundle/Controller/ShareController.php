<?php

namespace AppAdminBundle\Controller;


use AppAdminBundle\Form\Type\SonataShareFiltersType;
use AppBundle\Entity\ProductModels;
use AppBundle\Entity\ProductModelSpecificSize;
use AppBundle\Entity\ShareSizesGroup;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class: ShareController
 *
 * @see BaseController
 */
class ShareController extends BaseController
{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSizesGroupAction()
    {
        $sizesGroup = new ShareSizesGroup();
        $sizesGroup->setName('Новая группа');
        $sizesGroup->setShare($this->admin->getSubject());

        $em = $this->getDoctrine()->getManager();

        $em->persist($sizesGroup);
        $em->flush();

        return $this->renderGroups();
    }

    /**
     * @param ShareSizesGroup $group
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("group", options={"id" = "sizes_group_id"})
     */
    public function removeSizesGroupAction(ShareSizesGroup $group)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($group);
        $em->flush();

        return $this->renderGroups();
    }

    /**
     * @param Request $request
     * @param ShareSizesGroup $group
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("group", options={"id" = "sizes_group_id"})
     */
    public function updateSizesGroupAction(Request $request, ShareSizesGroup $group)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('name')) {
            $group->setName($request->get('name'));
        }

        if ($request->get('discount')) {
            $group->setDiscount($request->get('discount'));
        }

        foreach ($request->get('discounts', []) as $discountId => $discount) {
            $companionGroup = $em->getReference('AppBundle:ShareSizesGroup', $discountId);
            $this->get('share')->saveDiscountForCompanion($group, $companionGroup, $discount);
        }

        $em->persist($group);
        $em->flush();

        return $this->renderJson(['message' => 'Сохранено']);
    }

    /**
     * @param Request $request
     * @param ShareSizesGroup $group
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("group", options={"id" = "sizes_group_id"})
     */
    public function getFiltersSizesAction(Request $request, ShareSizesGroup $group)
    {
        $admin = $this->admin;

        $filtersForm = $this->createForm(SonataShareFiltersType::class, null, [
            'group' => $group
        ]);

        $filtersForm->handleRequest($request);

        // Save group updates in database only when "save" button is clicked
        if ($filtersForm->isSubmitted()) {
            $this->admin->updateGroup($group, $filtersForm->getData(), $filtersForm->get('save')->isClicked());
        }

        $models = $this->admin->paginateModels($group, $request->request->all());
        $filtersForm = $filtersForm->createView();

        return $this->renderJson([
            'sizes' => $this->render('AppAdminBundle:admin/shares/sizes_select_filters.html.twig',
                compact('models', 'filters', 'admin', 'filtersForm', 'categories', 'group'))->getContent()
        ]);
    }

    /**
     * @param Request $request
     * @param ShareSizesGroup $group
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("group", options={"id" = "sizes_group_id"})
     */
    public function getSizesAction(Request $request, ShareSizesGroup $group)
    {
        $admin = $this->admin;

        $models = $this->admin->paginateModelsToSelect($request->request->all());

        $categories = $this->getDoctrine()->getRepository('AppBundle:Categories')->findAll();

        $filters = $request->request->all();

        return $this->renderJson([
            'sizes' => $this->render('AppAdminBundle:admin/shares/sizes_select_items.html.twig',
                compact('models', 'filters', 'admin', 'group', 'categories'))->getContent()
        ]);
    }

    /**
     * @param Request $request
     * @param ShareSizesGroup $group
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("group", options={"id" = "sizes_group_id"})
     */
    public function getConflictSizesAction(Request $request, ShareSizesGroup $group)
    {
        $admin = $this->admin;

        $filters = $request->request->all();
        $filters['conflicts'] = true;
        $filters['group'] = $group;

        $models = $this->admin->paginateModelsToSelect($filters);

        $categories = $this->getDoctrine()->getRepository('AppBundle:Categories')->findAll();

        $filters = $request->request->all();

        return $this->renderJson([
            'sizes' => $this->render('AppAdminBundle:admin/shares/sizes_select_items.html.twig',
                compact('models', 'filters', 'admin', 'group', 'categories'))->getContent()
        ]);
    }

    /**
     * @param ShareSizesGroup $group
     * @param ProductModels $model
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("group", options={"id" = "sizes_group_id"})
     * @ParamConverter("model", options={"id" = "model_id"})
     */
    public function toggleGroupModelAction(ShareSizesGroup $group, ProductModels $model)
    {
        $this->get('share')->toggleGroupModel($group, $model);

        return $this->renderJson([]);
    }

    /**
     * @param ShareSizesGroup $group
     * @param ProductModelSpecificSize $specificSize
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("group", options={"id" = "sizes_group_id"})
     * @ParamConverter("specificSize", options={"id" = "size_id"})
     */
    public function toggleGroupSizeAction(ShareSizesGroup $group, ProductModelSpecificSize $specificSize)
    {
        $this->get('share')->toggleGroupSize($group, $specificSize);

        return $this->renderJson([]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderGroups()
    {
        return $this->renderJson([
            'groupsTab' => $this->renderView('AppAdminBundle:admin:share_groups.html.twig', [
                'admin' => $this->admin,
                'form_tab' => ['name' => 'Список групп']
            ])
        ]);
    }
}
