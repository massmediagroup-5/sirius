<?php

namespace AppBundle\Event;

class OrderListener
{
    protected $twig;
    protected $mailer;

    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer)
    {
        $this->twig = $twig;
        $this->mailer = $mailer;
    }

    public function onOrderCreated(OrderEvent $event)
    {
        $order = $event->getOrder();

        $body = $this->renderTemplate($order);

        $message = \Swift_Message::newInstance()
            ->setSubject('Order from orders@sirius.com.ua')
            ->setFrom('orders@sirius.com.ua')
            ->addTo('r.slobodzyanmassmedia@gmail.com','r.slobodzyanmassmedia@gmail.com')
            ->addTo('alisayatsyuk@gmail.com','alisayatsyuk@gmail.com')
            ->setBody($body)
            ->setContentType("text/html");
        $this->mailer->send($message);
    }

    public function renderTemplate($order)
    {
        return $this->twig->render('AppBundle:mails/order.html.twig', [
                'order' => $order
            ]
        );
    }
}