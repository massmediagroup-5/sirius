<?php

namespace AppImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AppImportBundle:Default:index.html.twig', array('name' => $name));
    }
}
