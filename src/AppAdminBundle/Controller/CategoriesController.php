<?php

namespace AppAdminBundle\Controller;

use Illuminate\Support\Str;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class: CategoriesController
 *
 * @see BaseController
 */
class CategoriesController extends BaseController
{
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

        if($object->getBasedProducts()->count()) {

            $this->addFlash(
                'sonata_flash_error',
                $this->admin->trans('flash_categories_delete_not_empty_error', [], 'AppAdminBundle')
            );

            return $this->redirectTo($object);
        }

        return parent::deleteAction($id);
    }
}
