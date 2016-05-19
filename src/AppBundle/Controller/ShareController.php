<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShareController
 * @package AppBundle\Controller
 */
class ShareController extends BaseController
{
    /**
     * @Route("/shares", name="shares_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $shares = $this->get('share')->paginateShares($request->request->all());

        return $this->render('AppBundle:shop/shares.html.twig', compact('shares'));
    }
}

