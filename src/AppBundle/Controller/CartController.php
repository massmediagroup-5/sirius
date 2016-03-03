<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{

    /**
     * @Route("/order-form", name="order-form", options={"expose"=true})
     */
    public function orderFormAction(Request $request)
    {
        if( (!empty($_POST['phone'])) && (!empty($_POST['fio'])) && (!empty($_POST['pay'])) )
        {
            $em = $this->getDoctrine()->getManager();
            $user_id = $this->get('users')->getCurrentUser();

            $delivery = $this->getDoctrine()->getRepository('AppBundle:Carriers')->findOneById($_POST['delivery']);
            $order = new \AppBundle\Entity\Orders();
            if($_POST['delivery'] == 1){
                $delivery_email = 'Новая почта';
                $prefix = 'np-';
            }elseif($_POST['delivery'] == 2){
                $delivery_email = 'Деливери';
                $prefix = 'del-';
            }
            if($_POST['delivery'] != 3){
                $cities = $this->getDoctrine()->getRepository('AppBundle:Cities')->findOneById($_POST[$prefix.'select']);
                $stores = $this->getDoctrine()->getRepository('AppBundle:Stores')->findOneById($_POST[$prefix.'sklad']);
                $delivery_email .= ', город - '.$cities->getName().', склад - '.$stores->getName();
                $order->setCities($cities);
                $order->setStores($stores);
            }

            if($_POST['delivery'] == 3){
                $delivery = $_POST['custom_delivery'];
                $order->setCustomDelivery($_POST['custom_delivery']);
            }
            $order->setUsers($user_id);
            $order->setPhone($_POST['phone']);
            $order->setCarriers($delivery);
            $order->setType('Обычный заказ');
            $order->setComment($_POST['comment']);
            $order->setPay($_POST['pay']);
            $order->setFio($_POST['fio']);
            $em->persist($order);

            $cart = $this->get('session')->get('cart_items');
            $items = array();
            $total_quantity = 0;
            $total_price = 0;
            foreach($cart as $key => $item){
                $sku = $this->getDoctrine()->getRepository('AppBundle:SkuProducts')->findOneById($key);
                $quantity = $item['quantity'];
                $total_quantity += $quantity;
                $items[$key]['name'] = $sku->getProductModels()->getName();
                $items[$key]['model_alias'] = $sku->getProductModels()->getAlias();
                $items[$key]['category_alias'] = $sku->getProductModels()->getProducts()->getProductsBaseCategories()->getCategories()->getAlias();
                $items[$key]['quantity'] = $quantity;
                $items[$key]['total_price'] = $quantity * $sku->getProductModels()->getPrice();
                $total_price += $items[$key]['total_price'];
                $cart = new \AppBundle\Entity\Cart();
                $cart->setUsers($user_id);
                $cart->setOrders($order);
                $cart->setSkuProducts($sku);
                $cart->setQuantity($quantity);
                $em->persist($cart);
            }
            $order->setTotalPrice($total_price);
            $em->persist($order);
            $em->flush();

            $body_insert = '<br />';
            if($_POST['delivery'] == 3)$delivery_email = 'Свой способ доставки: '.$_POST['custom_delivery'].'<br /><br />';
//            if(!empty($_POST['comment']))$body_insert .= 'Коментарий к заказу: '.$_POST['comment'].'<br /><br />';
            foreach($items as $item){
                $body_insert .= '<a href="http://mytex.com.ua/'.$item["category_alias"].'/'.$item["model_alias"].'">'.$item["name"].'</a>,'.
                ' количество - '.$item['quantity'].', сумма - '.$item['total_price'].'<br />';
            }

            $email_body = '<h2>Заказ mytex '.$order->getId().' </h2><hr>'.
                '<p>Номер телефона: '.preg_replace("/[^0-9]/", '', strip_tags($_POST['phone'])).' </p>'.
                '<p>'.$body_insert.' </p>'.
                '<p>Количество товаров: '.$total_quantity.' </p>'.
                '<p>Сумма заказа: '.$total_price.'грн </p>'.
                '<p>ФИО: '.$_POST['fio'].' </p>'.
                '<p>Доставка: '.$delivery_email.' </p>'.
                '<p>Способ оплаты: '.$_POST['pay'].' </p>';

            // sms to client
            $sms_client = $this->get('cart')->sendOrderSmsRequest('client', $_POST['phone'], $order->getId());
            if($sms_client['error'] == false){
                // если без ошибок то сохраняем идентификатор смс
                $order->setClientSmsId($sms_client['sms_id']);
            }else{
                // если ошибка то сохраняем текст ошибки
                $order->setClientSmsStatus($sms_client['error']);
            }
            // sms to manager
            $sms2 = $this->get('cart')->sendOrderSmsRequest('manager', '+380977838335', $order->getId(), $email_body);
            if($sms2['error'] == false){
                // если без ошибок то сохраняем идентификатор смс
                $order->setManagerSmsId($sms2['sms_id']);
            }else{
                // если ошибка то сохраняем текст ошибки
                $order->setManagerSmsStatus($sms2['error']);
            }

            $em->persist($order);
            $em->flush();
            $this->addFlash(
                'notice',
                'success'
            );

            try {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Order from orders@mytex.com.ua')
                    ->setFrom('orders@mytex.com.ua')
                    ->addTo('mytex777@gmail.com','mytex777@gmail.com')
                    ->addTo('test@massmedia.com.ua','test@massmedia.com.ua')
                    ->addTo('alisayatsyuk@gmail.com','alisayatsyuk@gmail.com')
                    ->addTo('mmyttexx@gmail.com','mmyttexx@gmail.com')
                    ->setBody($email_body)
                    ->setContentType("text/html")
                ;
                $this->get('mailer')->send($message);
            } catch (Exception $e){
            }

            $this->get('session')->set('cart_items',array());
            return $this->redirectToRoute('homepage', array(), 301);
        }else{
            return $this->redirectToRoute('order', array(), 301);
        }
    }

    /**
     * @Route("/order", name="order")
     */
    public function orderAction(Request $request)
    {
        $cart = $this->get('session')->get('cart_items');
        if(empty($cart))
            return $this->redirectToRoute('homepage', array(), 301);
        $order = array();
        foreach($cart as $key => $item){
            $order[$key]['item'] = $this->getDoctrine()->getRepository('AppBundle:SkuProducts')->findOneById($key);
            $order[$key]['quantity'] = $item['quantity'];
            $price = $order[$key]['item']->getProductModels()->getPrice();
            $order[$key]['total_price'] = $price * $item['quantity'];
        }
        $np = $this->getDoctrine()->getRepository('AppBundle:Cities')->findBy(array('carriers'=>1));
        $del = $this->getDoctrine()->getRepository('AppBundle:Cities')->findBy(array('carriers'=>2));
        $stores = $this->getDoctrine()->getRepository('AppBundle:Stores')->findAll();
        return $this->render('AppBundle:userpart:order.html.twig', array(
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..'),
            'params'            => $this->get('options')->getParams(),
            'cart'              => $this->get('cart')->getHeaderBasketInfo(),
            'compare'           => $this->get('compare')->getHeaderCompareInfo(),
            'recently_reviewed' => $this->get('entities')->getRecentlyViewed(),
            'order'             => $order,
            'np_city'           => $np,
            'del_city'          => $del,
            'stores'            => $stores,
        ));
    }
}
