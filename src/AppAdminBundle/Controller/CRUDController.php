<?php

namespace AppAdminBundle\Controller;

use Illuminate\Support\Str;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class: CRUDController
 *
 * @see BaseController
 */
class CRUDController extends BaseController
{
    /**
     * batchActionMerge
     *
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request             $request
     *
     * @return RedirectResponse
     */
    public function batchActionMerge(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $request = $this->get('request');
        $modelManager = $this->admin->getModelManager();

        $target = $modelManager->find($this->admin->getClass(), $request->get('targetId'));

        if ($target === null){
            $this->addFlash('sonata_flash_info', 'flash_batch_merge_no_target');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $selectedModels = $selectedModelQuery->execute();

        //dump($selectedModel);

        try {
            foreach ($selectedModels as $selectedModel) {
                $modelManager->delete($selectedModel);
            }

            $modelManager->update($selectedModel);
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_merge_error');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $this->addFlash('sonata_flash_success', 'flash_batch_merge_success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );
    }

    /**
     * Batch action activate
     *
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request             $request
     * @return RedirectResponse
     */
    public function batchActionActivate(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        $this->batchActionChange($selectedModelQuery, 'active', true, $request);
    }

    /**
     * Batch action deactivate
     *
     * @param ProxyQueryInterface $selectedModelQuery
     * @param Request             $request
     * @return RedirectResponse
     */
    public function batchActionDeactivate(ProxyQueryInterface $selectedModelQuery, Request $request = null)
    {
        $this->batchActionChange($selectedModelQuery, 'active', false, $request);
    }

    /**
     * Batch action change value
     *
     * @param ProxyQueryInterface $selectedModelQuery
     * @param $field
     * @param $value
     * @param Request|null $request
     * @return RedirectResponse
     */
    public function batchActionChange(ProxyQueryInterface $selectedModelQuery, $field, $value, Request $request = null)
    {
        if (!$this->admin->isGranted('EDIT') || !$this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $request = $this->get('request');
        $modelManager = $this->admin->getModelManager();

        $target = $modelManager->findBy($this->admin->getClass(), ['id' => $request->get('targetId')]);

        if ($target === null){
            $this->addFlash('sonata_flash_info', 'flash_batch_merge_no_target');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {
                $setter = 'set' . ucfirst(Str::camel($field));
                $selectedModel->$setter($value);
                $modelManager->update($selectedModel);
            }
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_merge_error');

            return new RedirectResponse(
                $this->admin->generateUrl('list', $this->admin->getFilterParameters())
            );
        }

        $this->addFlash('sonata_flash_success', 'flash_batch_merge_success');

        return new RedirectResponse(
            $this->admin->generateUrl('list', $this->admin->getFilterParameters())
        );
    }

}
