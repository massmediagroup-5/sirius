<?php

namespace AppAdminBundle\Controller;

use AppBundle\Entity\Categories;
use AppBundle\Entity\Products;
use Illuminate\Support\Str;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @param Request $request
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

        if ($target === null) {
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
     * @param Request $request
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
     * @param Request $request
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

        if ($target === null) {
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

    /**
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function cloneAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s',
                $this->admin->getIdParameter()));
        }

        $clonedObject = clone $object;  // Careful, you may need to overload the __clone method of your object

        $clonedObject = $this->admin->create($clonedObject);

        $this->addFlash('sonata_flash_success', 'Cloned successfully');

        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $clonedObject->getId()]));
    }

    /**
     * @inheritdoc
     */
    public function deleteAction($id)
    {
        $id     = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if ($object instanceof Categories) {
            $disallowDelete = (bool)($object->getBasedProducts()->count() + $object->getChildren()->count());
            $params = ['count'=>$object->getBasedProducts()->count(),'count1'=>$object->getChildren()->count()];
        } elseif($object instanceof Products) {
            $disallowDelete = (bool)$object->getProductModels()->count();
        } else {
            $disallowDelete = false;
        }

        if($disallowDelete) {
            if ($object instanceof Categories) {
                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans( 'flash_delete_not_empty_error', $params, 'AppAdminBundle' )
                );
            }elseif ($object instanceof Products) {
                $this->addFlash(
                    'sonata_flash_error',
                    $this->admin->trans( 'flash_delete_not_empty_error1', [ ], 'AppAdminBundle' )
                );
            }

            return $this->redirectTo($object);
        }

        return parent::deleteAction($id);
    }
}
