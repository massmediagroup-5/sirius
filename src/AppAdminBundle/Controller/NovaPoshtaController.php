<?php

namespace AppAdminBundle\Controller;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CoreController;

use NovaPoshta\Config;
use NovaPoshta\ApiModels\InternetDocument;

/**
 * Class: NovaPoshtaController
 *
 * @see Controller
 */
class NovaPoshtaController extends CoreController
{
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function getWaybillInfoAction(Request $request)
	{
        $api = $this->getDoctrine()->getRepository('AppBundle:Novaposhta')->findOneBy(['active'=>1]);
        Config::setApiKey($api->getApiKey());
        Config::setFormat(Config::FORMAT_JSONRPC2);
        Config::setLanguage(Config::LANGUAGE_RU);

		$data = new \NovaPoshta\MethodParameters\InternetDocument_documentsTracking();
		$data->setDocuments([$request->get('ttn')]);
		$track_info = InternetDocument::documentsTracking($data);

		$data = new \NovaPoshta\MethodParameters\InternetDocument_getDocumentList();
		$data->setIntDocNumber($request->get('ttn'));
		$ttn_info = InternetDocument::getDocumentList($data);

		return $this->render('AppAdminBundle:admin:search.html.twig', [
			'base_template' => $this->getBaseTemplate(),
			'admin_pool' => $this->container->get('sonata.admin.pool'),
			'blocks' => $this->container->getParameter('sonata.admin.configuration.dashboard_blocks'),
			'track_info' => $track_info,
			'ttn_info' => $ttn_info
		]);
	}

}
