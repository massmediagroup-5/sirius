<?php

namespace AppAdminBundle\Controller;

use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * BEGIN NOVAPOSHTA
 */

use NovaPoshta\Config;
use NovaPoshta\ApiModels\Counterparty;
use NovaPoshta\ApiModels\Address;
use NovaPoshta\ApiModels\InternetDocument;
use NovaPoshta\MethodParameters\Address_getStreet;
use NovaPoshta\MethodParameters\MethodParameters;
use NovaPoshta\MethodParameters\InternetDocument_getDocumentList;
use NovaPoshta\Models\CounterpartyContact;
use NovaPoshta\Models\OptionsSeat;

/**
 * END NOVAPOSHTA
 */

/**
 * Class: CRUDController
 *
 * @see BaseController
 */
class OrderController extends BaseController
{


    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function moveSizeAction(Request $request)
    {
        $object = $this->admin->getSubject();

        $size = $this->getDoctrine()
                     ->getManager()
                     ->getRepository('AppBundle:OrderProductSize')
                     ->find($request->get('size'));

        $this->get('order')->moveSize($object, $size, $request->get('quantity'));

        return $this->renderJson($this->renderPartials());
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function removeSizeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->get('order')->removeSize(
            $this->admin->getSubject(),
            $em->getRepository('AppBundle:OrderProductSize')->find($request->get('size'))
        );

        return $this->renderJson($this->renderPartials());
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function addSizesAction(Request $request)
    {
        $object = $this->admin->getSubject();

        $sizes = [];
        foreach ($request->get('sizes') as $sizeArray) {
            $sizes[] = [
                $this->getDoctrine()
                     ->getManager()
                     ->getRepository('AppBundle:ProductModelSpecificSize')
                     ->find($sizeArray['id']),
                $sizeArray['count']
            ];
        }

        $this->get('order')->addSizes($object, $sizes);

        return $this->renderJson($this->renderPartials());
    }

    /**
     * @return RedirectResponse
     */
    public function changePreOrderFlagAction()
    {
        $object = $this->admin->getSubject();

        $object = $this->get('order')->changePreOrderFlag($object);

        return $this->redirectTo($object);
    }

    /**
     * @return RedirectResponse
     */
    public function cancelOrderAction()
    {
        $object = $this->admin->getSubject();

        $this->get('order')->cancelOrder($object);

        $this->addFlash('sonata_flash_success', 'flash_cancel_order');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cancelOrderChangeAction(Request $request)
    {
        $object = $this->admin->getSubject();

        $historyItem = $this->get('history_manager')->createFromId($request->get('history_id'));
        
        if ($historyItem->rollback()) {
            $this->addFlash('sonata_flash_success', 'flash_order_history_item_canceled');
        } else {
            $this->addFlash('sonata_flash_error', 'flash_order_history_item_canceled_fail');
        }

        return new RedirectResponse($this->admin->generateUrl('edit', ['id' => $object->getId()]));
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxUpdateAction(Request $request)
    {
        $data   = Arr::only($request->request->all(), [
            'individualDiscount',
            'additionalSolarDescription',
            'additionalSolar',
        ]);
        $object = $this->admin->getSubject();

        $serializer = new Serializer([new ObjectNormalizer]);

        $object = $serializer->denormalize($data, 'AppBundle\\Entity\\Orders', null,
            ['object_to_populate' => $object]);

        $this->getDoctrine()->getManager()->persist($object);
        $this->getDoctrine()->getManager()->flush();

        return $this->renderJson([
            'history' => $this->renderView('AppAdminBundle:admin:order_history_items.html.twig', [
                'admin' => $this->admin
            ])
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxCreateWaybillAction(Request $request)
    {
        // Получаем ключ апи с базы и конфигуруем прослойку для работы с апи
        $api = $this->getDoctrine()->getRepository('AppBundle:Novaposhta')->findOneBy(['active' => 1]);
        Config::setApiKey($api->getApiKey());
        Config::setFormat(Config::FORMAT_JSONRPC2);
        Config::setLanguage(Config::LANGUAGE_RU);

        $orderObject = $this->admin->getSubject();
        $form_data   = $request->request->all();

        // Габариты груза
        $optionsSeat = new \NovaPoshta\Models\OptionsSeat();
        $optionsSeat->setVolumetricHeight($form_data['np_volumetric_height'])
                    ->setVolumetricLength($form_data['np_volumetric_length'])
                    ->setVolumetricWidth($form_data['np_volumetric_width'])
                    ->setWeight($form_data['np_weight']);

        // Город получателя
        $data = new \NovaPoshta\MethodParameters\Address_getCities();
        $data->setFindByString($orderObject->getCities()->getName());
        $result        = \NovaPoshta\ApiModels\Address::getCities($data);
        $cityRecipient = $result->data[0]->Ref;

        // Выбираем тип контрагента:
        $result           = \NovaPoshta\ApiModels\Common::getTypesOfCounterparties();
        $counterpartyType = $result->data[1]->Ref; // тип PrivatePerson

        // данные отправителя
        $data                       = new MethodParameters();
        $data->CounterpartyProperty = 'Sender';
        $data->FindByString         = 'Мудрицька';
        $result                     = Counterparty::getCounterparties($data);
        $senderInfo                 = $result->data[0]; // Полная информация о отправителе
        $citySender                 = $senderInfo->City; // Город отправителя
        $counterpartySender         = $senderInfo->Ref;

        // создаем контрагента получателя если до этого он небыл создан
        if ( ! $orderObject->getUsers()->getCounterpartyRef()) {
            $counterparty = new \NovaPoshta\ApiModels\Counterparty();
            $counterparty->setCounterpartyProperty(\NovaPoshta\ApiModels\Counterparty::RECIPIENT);
            $counterparty->setCityRef($cityRecipient);
            $counterparty->setCounterpartyType($counterpartyType);
            $counterparty->setFirstName($orderObject->getUsers()->getName());
            $counterparty->setLastName($orderObject->getUsers()->getSurname());
            $counterparty->setMiddleName($orderObject->getUsers()->getMiddlename());
            $counterparty->setPhone(preg_replace("/[^0-9]/", '', strip_tags($orderObject->getUsers()->getPhone())));
            $counterparty->setEmail($orderObject->getUsers()->getEmail());
            $result = $counterparty->save();

            $counterpartyRecipient = $result->data[0]->Ref;
            $orderObject->getUsers()->setCounterpartyRef($counterpartyRecipient);
        } else {
            $counterpartyRecipient = $orderObject->getUsers()->getCounterpartyRef();
        }

        // Получим контактных персон для контрагентов:
        $data = new \NovaPoshta\MethodParameters\Counterparty_getCounterpartyContactPersons();
        $data->setRef($counterpartySender);
        $result = \NovaPoshta\ApiModels\Counterparty::getCounterpartyContactPersons($data);

        $contactPersonSender = $result->data[0]->Ref;

        $data = new \NovaPoshta\MethodParameters\Counterparty_getCounterpartyContactPersons();
        $data->setRef($counterpartyRecipient);
        $result = \NovaPoshta\ApiModels\Counterparty::getCounterpartyContactPersons($data);

        $contactPersonRecipient = $result->data[0]->Ref;

        // Для контрагента отправителя получим склад отправки:
        $addressSender = $api->getWarehouseRef();

        // Создадим адрес для получателя:
        $addressRecipient = $orderObject->getStores()->getRef();

        // Теперь получим тип услуги:
        $result      = \NovaPoshta\ApiModels\Common::getServiceTypes();
        $serviceType = $result->data[2]->Ref; // Выбрали: WarehouseWarehouse

        // Выбираем плательщика:
        $payerType = $form_data['np_delivery_payer'];

        // Форму оплаты:
        $result        = \NovaPoshta\ApiModels\Common::getPaymentForms();
        $paymentMethod = $result->data[1]->Ref; // Выбрали: Cash

        // Тип груза:
        $result    = \NovaPoshta\ApiModels\Common::getCargoTypes();
        $cargoType = $result->data[0]->Ref; // Выбрали: Cargo

        // Мы выбрали все данные которые нам нужны для создания ЭН. Создаем ЭН:
        // Контрагент отправитель
        $sender = new \NovaPoshta\Models\CounterpartyContact();
        $sender->setCity($citySender)
               ->setRef($counterpartySender)
               ->setAddress($addressSender)
               ->setContact($contactPersonSender)
               ->setPhone(preg_replace("/[^0-9]/", '', strip_tags($api->getPhone())));

        // Контрагент получатель
        $recipient = new \NovaPoshta\Models\CounterpartyContact();
        $recipient->setCity($cityRecipient)
                  ->setRef($counterpartyRecipient)
                  ->setAddress($addressRecipient)
                  ->setContact($contactPersonRecipient)
                  ->setPhone(preg_replace("/[^0-9]/", '', strip_tags($orderObject->getPhone())));

        // Получаем тип платильщика обратной доставки с формы
        $redeliveryPayer = $form_data['np_backward_delivery_payer'];

        // Тип обратной доставки
        $redeliveryCargoType = 'Money';

        // Обратная доставка ценные бумаги
        $backwardDeliveryData = new \NovaPoshta\Models\BackwardDeliveryData();
        $backwardDeliveryData->setPayerType($redeliveryPayer);
        $backwardDeliveryData->setCargoType($redeliveryCargoType);
        $backwardDeliveryData->setRedeliveryString($form_data['np_backward_delivery_cost']);

        // И НАКОНЕЦ-ТО формирование ТТН
        $internetDocument = new \NovaPoshta\ApiModels\InternetDocument();
        $internetDocument->setSender($sender)
                         ->setRecipient($recipient)
                         ->setServiceType($serviceType)
                         ->setPayerType($payerType)
                         ->setPaymentMethod($paymentMethod)
                         ->setCargoType($cargoType)
                         ->setWeight($form_data['np_weight'])
                         ->setSeatsAmount($form_data['np_seats_amount'])
                         ->setCost($form_data['np_cost'])
                         ->setDescription('Sirius')
                         ->setDateTime($form_data['np_date'])
                         ->addOptionsSeat($optionsSeat)
                         ->addBackwardDeliveryData($backwardDeliveryData);
        $result = $internetDocument->save();

        $refInternetDocument = $result->data[0]->Ref;
        $orderObject->setTtn($refInternetDocument);

        $this->getDoctrine()->getManager()->persist($orderObject);
        $this->getDoctrine()->getManager()->flush();

        return $this->renderJson(['status' => 'OK']);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxUpdateWaybillAction(Request $request)
    {
        $data = new \NovaPoshta\MethodParameters\InternetDocument_getDocumentList();
        $data->setIntDocNumber($request->get('np_ttn'));
        $document = InternetDocument::getDocumentList($data);

        if ($document->data) {
            $ttn = $document->data[0];
            $orderObject = $this->admin->getSubject();
            $orderObject->setTtn($ttn->Ref);

            $this->getDoctrine()->getManager()->persist($orderObject);
            $this->getDoctrine()->getManager()->flush();
            return $this->renderJson([]);
        }
        
        return $this->renderJson(['message' => 'Неверный ТТН'], 422);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxPrintWaybillAction(Request $request)
    {
        // Получаем ключ апи с базы и конфигуруем прослойку для работы с апи
        $api = $this->getDoctrine()->getRepository('AppBundle:Novaposhta')->findOneBy(['active' => 1]);
        Config::setApiKey($api->getApiKey());
        Config::setFormat(Config::FORMAT_JSONRPC2);
        Config::setLanguage(Config::LANGUAGE_RU);

        $data = new \NovaPoshta\MethodParameters\InternetDocument_printDocument();
        $data->addDocumentRef($this->admin->getSubject()->getTtn());
        $data->setType(\NovaPoshta\ApiModels\InternetDocument::PRINT_TYPE_PDF);
        $data->setCopies(\NovaPoshta\ApiModels\InternetDocument::PRINT_COPIES_DOUBLE);

        $link = \NovaPoshta\ApiModels\InternetDocument::printDocument($data);

        return $this->renderJson([
            'status' => 'OK',
            'link'   => $link
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxDeleteWaybillAction(Request $request)
    {
        $orderObject = $this->admin->getSubject();
        // Получаем ключ апи с базы и конфигуруем прослойку для работы с апи
        $api = $this->getDoctrine()->getRepository('AppBundle:Novaposhta')->findOneBy(['active' => 1]);
        Config::setApiKey($api->getApiKey());
        Config::setFormat(Config::FORMAT_JSONRPC2);
        Config::setLanguage(Config::LANGUAGE_RU);

        $internetDocument = new InternetDocument();
        $internetDocument->setRef($orderObject->getTtn());
        $result = $internetDocument->delete();

        $orderObject->setTtn(null);

        $this->getDoctrine()->getManager()->persist($orderObject);
        $this->getDoctrine()->getManager()->flush();

        return $this->renderJson([
            'status' => 'OK',
            'result' => $result
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSizesAction(Request $request)
    {
        $admin = $this->admin;

        $filters = $request->request->all();

        $models = $this->admin->paginateModels($filters);

        $categories = $this->getDoctrine()->getRepository('AppBundle:Categories')->findAll();

        return $this->renderJson([
            'sizes' => $this->renderView('AppAdminBundle:admin:order_sizes_select.html.twig',
                compact('models', 'categories', 'filters', 'admin'))
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createFromCallbackAction(Request $request)
    {
        $request = $this->getRequest();

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->createFromCallback($request->get('id'));

        $this->admin->setSubject($object);

        return $this->redirectTo($object);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderPartials()
    {
        $parameters = [
            'admin'    => isset( $parameters['admin'] ) ? $parameters['admin'] : $this->admin,
            'form_tab' => [
                'name' => 'Список заказанных товаров'
            ]
        ];

        return [
            'partial' => $this->renderView('AppAdminBundle:admin:order_sizes.html.twig', $parameters),
            'history' => $this->renderView('AppAdminBundle:admin:order_history_items.html.twig', [
                'admin' => $this->admin
            ])
        ];
    }
}
