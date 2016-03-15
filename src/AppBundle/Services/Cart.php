<?php

namespace AppBundle\Services;

use AppBundle\Entity\CheckAvailability;
use Doctrine\ORM\EntityManager;
use Proxies\__CG__\AppBundle\Entity\FollowThePrice;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * Class: Cart
 * @author A.Kravchuk
 */
class Cart
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * templating
     *
     * @var mixed
     */
    private $templating;

    /**
     * container
     *
     * @var mixed
     */
    private $container;

    /**
     * session
     *
     * @var mixed
     */
    private $session;

    /**
     * __construct
     *
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(EntityManager $em, ContainerInterface $container, Session $session)
    {
        $this->em = $em;
        $this->container = $container;
        $this->session = $session;
        $this->templating = $container->get('templating');
    }

    /**
     * addItemToCard
     *
     * @param integer $sku_id
     *
     */
    public function addItemToCard($sku_id)
    {
        $cart = $this->session->get('cart_items');
        if(!$cart) $cart = array();
        if(isset($cart[$sku_id])){
            $qty = $cart[$sku_id]['quantity'];
            $cart[$sku_id]['quantity'] = ++$qty;
        }else{
            $cart[$sku_id] = array(
                'sku'=> $sku_id,
                'quantity' => 1
            );
        }
        $this->session->set('cart_items', $cart);
    }

    /**
     * sendOrderSmsRequest
     *
     * @param mixed $message_type
     * @param mixed $phone
     * @param integer $order_id
     * @param string $email_text
     *
     * return mixed
     */
    public function sendOrderSmsRequest($message_type, $phone, $order_id, $email_text = null)
    {
        if( $curl = curl_init() ) {
            $now = time();
            $hour = date('G', $now);
            $phone = preg_replace("/[^0-9]/", '', strip_tags($phone));
            // текст в зависимости от времени суток
            if ($hour >= 21 or $hour <= 8)
            {// если вечер/ночь
                switch ($message_type)
                {
                    case 'manager':
                        $sms_body = mb_substr(trim(preg_replace('/[\s]+/ui', ' ', strip_tags(html_entity_decode($email_text,
                            ENT_QUOTES, 'utf-8')))), 0, 70);
                        break;
                    default:
                        $sms_body = sprintf(
                            "Заказ %s принят. Мы свяжемся с Вами утром, после 10:30. +38097 783 83 35",
                            $order_id
                        );
                        break;
                }

            }
            else
            {// если утро/день
                switch ($message_type)
                {
                    case 'manager':
                        $sms_body = mb_substr(trim(preg_replace('/[\s]+/ui', ' ', strip_tags(html_entity_decode($email_text,
                            ENT_QUOTES, 'utf-8')))), 0, 70);
//                        dump($sms_body);
//                        dump($phone);exit;
                        break;
                    default:
                        $sms_body = sprintf(
                            "Заказ %s принят. Скоро с Вами свяжется менеджер. +38097 783 83 35",
                            $order_id
                        );
                        break;
                }
            }
            // массив передаваемых параметров
            $parameters_array = array (
                'api_key'   => "5c8ore8ajzb8qg8jmyxayhx6ye375a4e86sdmife",// майтех
//                'api_key'   => "5mbn33td55pew683nyjto4hn881iy7xw58gnhw4a",// 4ребенка
                'phone'     => $phone,
                'sender'    => "sirius",
                'text'      => $sms_body
            );

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters_array);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_URL, 'http://api.unisender.com/ru/api/sendSms?format=json');
            $response = curl_exec($curl);

            if ($response) {
                // Раскодируем ответ API-сервера
                $jsonObj = json_decode($response);
                if(null===$jsonObj) {
                    // Ошибка в полученном ответе
                    $result['error'] = "Invalid JSON";
                }
                elseif(!empty($jsonObj->result->error)) {
                    // Ошибка отправки сообщения
                    $result['error'] = "An error occured: " . $jsonObj->result->error . "(code: " . $jsonObj->result->code . ")";
                } else {
                    // Сообщение успешно отправлено
//                    dump($jsonObj);exit;
                    $result['sms_id'] = $jsonObj->result->sms_id;
                    $result['error'] = false;
                }
            } else {
                // Ошибка соединения с API-сервером
                $result['error'] = "API access error";
            }
            curl_close($curl);
            return $result;
        }
    }

    /**
     * oneClickOrder
     *
     * @param integer $sku_id
     * @param string $phone
     *
     */
    public function oneClickOrder($sku_id, $phone)
    {
        $user_id = $this->container->get('users')->getCurrentUser();
        $sku = $this->em->getRepository('AppBundle:SkuProducts')
            ->findOneById($sku_id);

        $order = new \AppBundle\Entity\Orders();
        $order->setUsers($user_id);
        $order->setPhone($phone);
        $order->setType('Заказ в 1 клик');
        $order->setTotalPrice($sku->getProductModels()->getPrice());
        $this->em->persist($order);

        $cart = new \AppBundle\Entity\Cart();
        $cart->setUsers($user_id);
        $cart->setOrders($order);
        $cart->setSkuProducts($sku);
        $cart->setQuantity(1);
        $this->em->persist($cart);

        $this->em->flush();

        $name = $sku->getProductModels()->getName();
        $model_alias = $sku->getProductModels()->getAlias();
        $category_alias = $sku->getProductModels()->getProducts()->getBaseCategory()->getAlias();
        $price = $sku->getProductModels()->getPrice();

        $message = \Swift_Message::newInstance()
            ->setSubject('Order from orders@sirius.com.ua')
            ->setFrom('orders@sirius.com.ua')
            ->addTo('sirius777@gmail.com','sirius777@gmail.com')
            ->addTo('test@massmedia.com.ua','test@massmedia.com.ua')
            ->addTo('alisayatsyuk@gmail.com','alisayatsyuk@gmail.com')
            ->addTo('mmyttexx@gmail.com','mmyttexx@gmail.com')
            ->setBody('<h2>Новый заказ в 1 клик с сайта sirius.com.ua!</h2><hr>'.
                '<p>Номер телефона: '.$phone.'</p>'.
                '<p><a href="http://sirius.com.ua/'.$category_alias.'/'.$model_alias.'">'.$name.'</a></p>'.
                '<p>Сумма заказа: '.$price.'грн</p>'
            )
            ->setContentType("text/html")
        ;

        $this->container->get('mailer')->send($message);
    }

    /**
     * byInCredit
     *
     * @param integer $sku_id
     * @param string $name
     * @param string $city
     * @param string $phone
     *
     */
    public function byInCredit($sku_id, $name, $city, $phone)
    {
        $user_id = $this->container->get('users')->getCurrentUser();
        $sku = $this->em->getRepository('AppBundle:SkuProducts')
            ->findOneById($sku_id);

        $order = new \AppBundle\Entity\Orders();
        $order->setUsers($user_id);
        $order->setFio($name);
        $order->setComment($city);
        $order->setPhone($phone);
        $order->setType('Кредит');
        $order->setTotalPrice($sku->getProductModels()->getPrice());
        $this->em->persist($order);

        $cart = new \AppBundle\Entity\Cart();
        $cart->setUsers($user_id);
        $cart->setOrders($order);
        $cart->setSkuProducts($sku);
        $cart->setQuantity(1);
        $this->em->persist($cart);

        $this->em->flush();

        $credit = $this->session->get('credit');
        if(!$credit)$credit = array();
        $credit[$sku->getProductModels()->getId()] = $sku->getProductModels()->getId();
        $this->session->set('credit', $credit);


        $name = $sku->getProductModels()->getName();
        $model_alias = $sku->getProductModels()->getAlias();
        $category_alias = $sku->getProductModels()->getProducts()->getBaseCategory()->getAlias();
        $price = $sku->getProductModels()->getPrice();

        $message = \Swift_Message::newInstance()
            ->setSubject('Order from orders@sirius.com.ua')
            ->setFrom('orders@sirius.com.ua')
            ->addTo('sirius777@gmail.com','sirius777@gmail.com')
            ->addTo('test@massmedia.com.ua','test@massmedia.com.ua')
            ->addTo('alisayatsyuk@gmail.com','alisayatsyuk@gmail.com')
            ->addTo('mmyttexx@gmail.com','mmyttexx@gmail.com')
            ->setBody('<h2>Новый запрос на покупку в кредит с сайта sirius.com.ua!</h2><hr>'.
                '<p>Номер телефона: '.$phone.'</p>'.
                '<p>ФИО: '.$name.'</p>'.
                '<p>Город: '.$city.'</p>'.
                '<p><a href="http://sirius.com.ua/'.$category_alias.'/'.$model_alias.'">'.$name.'</a></p>'.
                '<p>Сумма заказа: '.$price.'грн</p>'
            )
            ->setContentType("text/html")
        ;

        $this->container->get('mailer')->send($message);
    }

    /**
     * FollowThePrice
     *
     * @param integer $sku_id
     * @param string $email
     *
     */
    public function FollowThePrice($sku_id, $email)
    {
        $user_id = $this->container->get('users')->getCurrentUser();
        $sku = $this->em->getRepository('AppBundle:SkuProducts')
            ->findOneById($sku_id);

        $follow = new FollowThePrice();
        $follow->setUsers($user_id);
        $follow->setProductModels($sku->getProductModels());
        $follow->setEmail($email);
        $this->em->persist($follow);
        $this->em->flush();

        $spy = $this->session->get('spy');
        if(!$spy)$spy = array();
        $spy[$sku->getProductModels()->getId()] = $sku->getProductModels()->getId();
        $this->session->set('spy', $spy);
    }

    /**
     * reportThePresence
     *
     * @param integer $sku_id
     * @param string $email
     *
     */
    public function reportThePresence($sku_id, $email)
    {
        $user_id = $this->container->get('users')->getCurrentUser();
        $sku = $this->em->getRepository('AppBundle:SkuProducts')
            ->findOneById($sku_id);
        $report = new CheckAvailability();
        $report->setUsers($user_id);
        $report->setEmail($email);
        $report->setProductModels($sku->getProductModels());
        $this->em->persist($report);
        $this->em->flush();
    }

    /**
     * increasingQuantity
     *
     * @param integer $sku_id
     *
     */
    public function increasingQuantity($sku_id)
    {
        $cart = $this->session->get('cart_items');
        if(isset($cart[$sku_id])){
            $qty = $cart[$sku_id]['quantity'];
            $cart[$sku_id]['quantity'] = ++$qty;
        }
        $this->session->set('cart_items', $cart);
    }

    /**
     * decreaseQuantity
     *
     * @param integer $sku_id
     *
     */
    public function decreaseQuantity($sku_id)
    {
        $cart = $this->session->get('cart_items');
        if(isset($cart[$sku_id])){
            $qty = $cart[$sku_id]['quantity'];
            $cart[$sku_id]['quantity'] = --$qty;
        }
        $this->session->set('cart_items', $cart);
    }

    /**
     * removeFromCart
     *
     * @param integer $sku_id
     *
     */
    public function removeFromCart($sku_id)
    {
        $cart = $this->session->get('cart_items');
        if(isset($cart[$sku_id])){
            unset($cart[$sku_id]);
        }
        $this->session->set('cart_items', $cart);
    }

    /**
     * Return array with Cart information
     *
     * @return mixed
     */
    public function getInfo()
    {
        $cart = $this->session->get('cart_items');
        $credit = $this->session->get('credit');
        $spy = $this->session->get('spy');

        $result = array(
            'cart_header_tpl'=> '',
            'cart_items_tpl'=> array(),
            'qty'=>0,
            'total_price'=>0,
            'credit_ids'=>array(),
            'spy_ids'=>array(),
        );
        if($credit){
            $result['credit_ids'] = $credit;
        }
        if($spy){
            $result['spy_ids'] = $spy;
        }

        if($cart){
            foreach($cart as $item){
                $item['sku'] = $this->em->getRepository('AppBundle:SkuProducts')->findOneById($item['sku']);
                $price = $item['sku']->getProductModels()->getPrice();
                $qty = $item['quantity'];
                $result['total_price'] += $price * $qty;
                $result['qty'] +=$qty;
            }
        }

        return $result;
    }

}
