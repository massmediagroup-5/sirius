<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class AjaxController extends Controller
{
    private $result = array();

    /**
     * @Route("/ajax/callback", name="callback", options={"expose"=true})
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function callbackAction(Request $request)
    {
        if($request->get('phone')){
            $phone = new \AppBundle\Entity\CallBack();
            $phone->setPhone($request->get('phone'));
//            $phone->setUsers($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($phone);
            $em->flush();

            $message = \Swift_Message::newInstance()
                ->setSubject('Callback from orders@mytex.com.ua')
                ->setFrom('callback@mytex.com.ua')
                ->setTo('mytex777@gmail.com')
                ->setBody('<h2>Запрос на обратный звонок с сайта mytex.com.ua!</h2><hr>'.
                    '<p>Номер телефона: '.$request->get('phone').'</p>'
                )
                ->setContentType("text/html")
            ;

            $this->container->get('mailer')->send($message);

            $this->result['status'] = 'OK';
            return new JsonResponse($this->result);
        }else{
            $this->result['message'] = 'Type phone number';
            $this->result['status'] = 'ERROR';
            return new JsonResponse($this->result, 422);
        }
    }

    /**
     * @Route("/ajax/one_click_order", name="one_click_order", options={"expose"=true})
     */
    public function oneClickOrder(Request $request)
    {
        if($request->get('phone') && $request->get('sku')){
            $this->get('cart')->oneClickOrder($request->get('sku'),$request->get('phone'));

            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/report_the_presence", name="report_the_presence", options={"expose"=true})
     */
    public function reportThePresence(Request $request)
    {
        if($request->get('sku') && $request->get('email')){
            $this->get('cart')->reportThePresence($request->get('sku'), $request->get('email'));

            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/follow_the_price", name="follow_the_price", options={"expose"=true})
     */
    public function FollowThePrice(Request $request)
    {
        if($request->get('sku') && $request->get('email')){
            $this->get('cart')->FollowThePrice($request->get('sku'), $request->get('email'));
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/buy_in_credit", name="buy_in_credit", options={"expose"=true})
     */
    public function buyInCredit(Request $request)
    {
        if($request->get('sku') && $request->get('name') && $request->get('city') && $request->get('phone')){
            $this->get('cart')->byInCredit($request->get('sku'),$request->get('name'), $request->get('city'), $request->get('phone'));
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/add_to_cart", name="add_to_cart", options={"expose"=true})
     */
    public function addToCartAction(Request $request)
    {
        if($request->get('sku_id')){
            $this->get('cart')->addItemToCard($request->get('sku_id'));

            $this->result['basket'] = $this->get('cart')->getHeaderBasketInfo();

            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/delete_cart_item", name="delete_cart_item", options={"expose"=true})
     */
    public function deleteCartItem(Request $request)
    {
        if($request->get('sku_id')){
            $this->get('cart')->removeFromCart($request->get('sku_id'));

            $this->result['basket'] = $this->get('cart')->getHeaderBasketInfo();
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/increasing_cart_qty", name="increasing_cart_qty", options={"expose"=true})
     */
    public function increasingCartQuantity(Request $request)
    {
        if($request->get('cart_id')){
            $this->get('cart')->increasingQuantity($request->get('cart_id'));
            
            $this->result['basket'] = $this->get('cart')->getHeaderBasketInfo();
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/decrease_cart_qty", name="decrease_cart_qty", options={"expose"=true})
     */
    public function decreaseCartQuantity(Request $request)
    {
        if($request->get('cart_id')){
            $this->get('cart')->decreaseQuantity($request->get('cart_id'));

            $this->result['basket'] = $this->get('cart')->getHeaderBasketInfo();
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/get_stores_by_city", name="get_stores_by_city", options={"expose"=true})
     */
    public function getStoresByCityId(Request $request)
    {
        if($request->get('city_id')){
            $em = $this->getDoctrine()->getManager()->getRepository('AppBundle:Stores');
            $query = $em->createQueryBuilder('s')
                ->where('s.cities = :city_id')
                ->setParameter('city_id', $request->get('city_id'))
                ->getQuery()
            ;
            $data = $query->getArrayResult();
            $this->result['stores'] = $data;
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/add_to_compare", name="add_to_compare", options={"expose"=true})
     */
    public function addToCompare(Request $request)
    {
        if($request->get('category_id') && $request->get('model_id')){
            $this->get('compare')->addToCompare($request->get('category_id'),$request->get('model_id'));
            $this->result['compare'] = $this->get('compare')->getHeaderCompareInfo();
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/remove_from_compare", name="remove_from_compare", options={"expose"=true})
     */
    public function removeFromCompare(Request $request)
    {
        if($request->get('category_id') && $request->get('model_id')){
            $this->get('compare')->removeFromCompare($request->get('category_id'),$request->get('model_id'));
            $compare = $this->get('compare')->getHeaderCompareInfo();
            $this->result['compare'] = $compare;
            if($compare['compare_qty'] == 0){
                $data = $this->getDoctrine()->getRepository('AppBundle:Categories')->findOneBy(
                    array('active'=>1,'inMenu'=>1,'id'=>$request->get('category_id'))
                );
                $this->result['url'] = $data->getAlias();
                $this->result['del'] = 'redirect';
            }else{
                foreach($compare['compare_ids'] as $category_id => $model_ids){
                    $models = $this->getDoctrine()->getRepository('AppBundle:ProductModels')->findBy(
                        array('active'=>1,'published'=>1,'id'=>array_keys($model_ids['models'])),
                        array('id'=>'ASC')
                    );
                    $compare['categories'][$compare['compare_ids'][$category_id]['date']]['models'] = $models;
                }
                $this->result['tpl'] = $this->render('AppBundle:userpart:compare_lists.html.twig',array('compare'=>$compare))->getContent();
                $this->result['del'] = 'delete';
            }
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/remove_category_from_compare", name="remove_category_from_compare", options={"expose"=true})
     */
    public function removeCategoryFromCompare(Request $request)
    {
        if($request->get('category_id')){
            $this->get('compare')->removeCategoryFromCompare($request->get('category_id'));
            $compare = $this->get('compare')->getHeaderCompareInfo();
            $this->result['compare'] = $compare;
            if($compare['compare_qty'] == 0){
                $data = $this->getDoctrine()->getRepository('AppBundle:Categories')->findOneBy(
                    array('active'=>1,'inMenu'=>1,'id'=>$request->get('category_id'))
                );
                $this->result['url'] = $data->getAlias();
                $this->result['del'] = 'redirect';
            }else{
                foreach($compare['compare_ids'] as $category_id => $model_ids){
                    $models = $this->getDoctrine()->getRepository('AppBundle:ProductModels')->findBy(
                        array('active'=>1,'published'=>1,'id'=>array_keys($model_ids['models'])),
                        array('id'=>'ASC')
                    );
                    $compare['categories'][$compare['compare_ids'][$category_id]['date']]['models'] = $models;
                }
                $this->result['tpl'] = $this->render('AppBundle:userpart:compare_lists.html.twig',array('compare'=>$compare))->getContent();
                $this->result['del'] = 'delete';
            }
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

    /**
     * @Route("/ajax/get_order_info_by_phone", name="get_order_info_by_phone", options={"expose"=true})
     */
    public function getOrderInfoByPhone(Request $request)
    {
        if($request->get('phone')){
            $data = $this->getDoctrine()->getRepository('AppBundle:Orders')->findOneBy(array(
                'phone'=> $request->get('phone'),
                'type' => 'Обычный заказ'
            ));

            if($data){
                $this->result['info']['fio'] = $data->getFio();
                $this->result['info']['pay'] = $data->getPay();
                $this->result['info']['carriers'] = $data->getCarriers()->getId();
                if(!$data->getCustomDelivery()){
                    $this->result['info']['cities'] = $data->getCities()->getId();
                    $this->result['info']['stores'] = $data->getStores()->getId();
                }else{
                    $this->result['info']['custom_delivery'] = $data->getCustomDelivery();
                }
            }
            $this->result['status'] = 'OK';
        }else{
            $this->result['status'] = 'ERROR';
        }
        return new Response(json_encode($this->result));
    }

}
