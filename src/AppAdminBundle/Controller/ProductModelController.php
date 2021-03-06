<?php

namespace AppAdminBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class: CRUDController
 *
 * @see BaseController
 */
class ProductModelController extends CRUDController
{
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancelProductModelChangeAction(Request $request)
    {
        $object = $this->admin->getSubject();

        $historyItem = $this->get('history_manager')->createFromId($request->get('history_id'));

        if ($historyItem->rollback()) {
            $this->addFlash('sonata_flash_success', 'flash_order_history_item_canceled');
        } else {
            $this->addFlash('sonata_flash_error', 'flash_order_history_item_canceled_fail');
        }

        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $object->getId()]));
    }
}
