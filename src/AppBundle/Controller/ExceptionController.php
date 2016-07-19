<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class: ExceptionController
 *
 * @see Controller
 */
class ExceptionController extends Controller
{

    /**
     * indexAction
     *
     * @return mixed
     */
    public function indexAction()
    {
        return $this->render('AppBundle:error.html.twig');
    }

    /**
     * indexAction
     *
     * @return mixed
     */
    public function disableAction()
    {
        return $this->render('AppBundle:disable.html.twig');
    }
}
