<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SkuProducts;
use AppBundle\Form\Type\AddInCartType;
use AppBundle\Form\Type\ChangeProductSizeQuantityType;
use AppBundle\Form\Type\ChangeProductSizeType;
use AppBundle\Form\Type\CreateOrderType;
use AppBundle\Form\Type\QuickOrderType;
use AppBundle\Form\Type\RemoveProductSizeType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends BaseController
{
    /**
     * @return RedirectResponse
     */
    public function afterAction()
    {
        return $this->redirect($this->get('last_urls')->getLastRequestedUrl());
    }

    /**
     * @return RedirectResponse
     */
    public function connectServiceAction()
    {
        return $this->redirect($this->get('last_urls')->getLastRequestedUrl());
    }

}
