<?php

namespace AppAdminBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CoreController;

/**
 * Class: ImportController
 *
 * @see Controller
 */
class ImportController extends CoreController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm('AppAdminBundle\Form\ImportForm');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->startImport($form);
        }

        return $this->render('AppAdminBundle:admin:import.html.twig', [
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
            'form' => $form->createView()
        ]);
    }

    /**
     * Start import
     *
     * @param Form $form
     * @return RedirectResponse
     */
    protected function startImport(Form $form)
    {
        $formData = $form->getData();
        
        $data = [
            'base_template' => $this->getBaseTemplate(),
            'admin_pool' => $this->container->get('sonata.admin.pool'),
            'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
            'form' => $form->createView()
        ];
        
        $validation = $this->get('app.admin.import')->validateImport($formData['file'], $formData);

        $data['validation'] = $validation;

        if ($validation['errors']->count() == 0) {
            $this->get('app.admin.import')->import($formData['file'], $formData);

            $this->addFlash('sonata_flash_success', 'Успешно импортировано');
        } else {
            $this->addFlash('sonata_flash_error', "Ошибка валидации (с ошибками {$validation['errors']->count()} с " .
                "{$validation['data']->count()})");
        }
        
        return $this->render('AppAdminBundle:admin:import.html.twig', $data);
    }

}
