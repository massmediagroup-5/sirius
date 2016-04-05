<?php

namespace AppBundle\Services;

use AppBundle\Entity\Orders;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class: Humanizer
 * @author zimm
 */
class Humanizer
{

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * @var \Symfony\Component\Translation\DataCollectorTranslator
     */
    private $translator;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->translator = $this->container->get('translator');
    }

    /**
     * @param $order
     * @return string
     */
    public function orderStatus($order)
    {
        $status = $order instanceof Orders ? $order->getStatus() : $order;

        switch ($status) {
            case Orders::STATUS_WAIT:
                return $this->translator->trans('orders status wait');
            case Orders::STATUS_ACCEPTED:
                return $this->translator->trans('orders status accepted');
            case Orders::STATUS_REJECTED:
                return $this->translator->trans('orders status rejected');
            default:
                return $this->translator->trans('orders status wrong');
        }
    }

    /**
     * @param $order
     * @return string
     */
    public function orderPayType($order)
    {
        $pay = $order instanceof Orders ? $order->getPay() : $order;

        switch ($pay) {
            case Orders::PAY_TYPE_COD:
                return $this->translator->trans('orders pay cod');
            case Orders::PAY_TYPE_BANK_CARD:
                return $this->translator->trans('orders pay bank');
            default:
                return $this->translator->trans('orders pay wrong');
        }
    }

}
