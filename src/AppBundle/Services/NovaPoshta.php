<?php
/**
 * Created by PhpStorm.
 * User: TaPTaK
 * Date: 15.06.2016
 * Time: 9:40
 */

namespace AppBundle\Services;

use AppBundle\Entity\Carriers;
use AppBundle\Entity\Cities;
use AppBundle\Entity\NovaposhtaSender;
use AppBundle\Entity\Stores;
use Doctrine\ORM\EntityManager;
use NovaPoshta\ApiModels\Address;
use NovaPoshta\ApiModels\ContactPerson;
use NovaPoshta\ApiModels\Counterparty;
use NovaPoshta\ApiModels\InternetDocument;
use NovaPoshta\Config;
use NovaPoshta\MethodParameters\Address_getWarehouses;
use NovaPoshta\MethodParameters\Address_getCities;
use NovaPoshta\MethodParameters\MethodParameters;
use NovaPoshta\Models\DataContainerResponse;

/**
 * Class: NovaPoshta
 *
 */
class NovaPoshta
{

    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * __construct
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * insertDatabase
     *
     * @return null
     */
    public function insertDatabase()
    {
        ini_set('max_execution_time', 3600);
        $carrier = $this->em->getRepository('AppBundle:Carriers')->findOneBy(['name' => 'Новая почта']);

        // Get and insert new Cities
        $cities = Address::getCities();
        foreach ($cities->data as $city) {
            $cityObject = new Cities();
            $cityObject->setCarriers($carrier);
            $cityObject->setName($city->DescriptionRu);
            $cityObject->setRef($city->Ref);
            $cityObject->setFullJson(json_encode($city));
            $cityObject->setActive(true);
            $this->em->persist($cityObject);
            $this->em->flush($cityObject);
        }

        // Get and insert new Warehouses
        $cities = $this->em->getRepository('AppBundle:Cities')->findAll();

        foreach ($cities as $city) {
            $data = new Address_getWarehouses();
            $data->setCityRef($city->getRef());
            $warehouses = Address::getWarehouses($data);

            foreach ($warehouses->data as $warehouse) {
                $warehouseObject = new Stores();
                $warehouseObject->setCities($city);
                $warehouseObject->setName($warehouse->DescriptionRu);
                $warehouseObject->setRef($warehouse->Ref);
                $warehouseObject->setFullJson(json_encode($warehouse));
                $warehouseObject->setActive(true);
                $this->em->persist($warehouseObject);
                $this->em->flush($warehouseObject);
            }
        }

    }

    /**
     * updateDatabase
     *
     * @return null
     */
    public function updateDatabase()
    {
        ini_set('max_execution_time', 3600);

        // Deactivate all cities and warehouses
        $this->em->createQueryBuilder()
            ->update('AppBundle:Cities', 'c')
            ->where('c.carriers = :carrier')
            ->setParameter('carrier', Carriers::NP_ID)
            ->set('c.active', '0')
            ->getQuery()
            ->execute();

        $npCitiesBuilder = $this->em->getRepository('AppBundle:Cities')
            ->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.carriers = :carrier');
        $this->em->createQueryBuilder()
            ->update('AppBundle:Stores', 's')
            ->where("s.cities IN ({$npCitiesBuilder->getDQL()})")
            ->set('s.active', '0')
            ->setParameter('carrier', Carriers::NP_ID)
            ->getQuery()
            ->execute();

        $carrier = $this->em->getRepository('AppBundle:Carriers')->findOneBy(['name' => 'Новая почта']);

        // Get and insert(update) new(old) Cities
        $cities = Address::getCities();
        foreach ($cities->data as $city) {
            $cityObject = $this->em->getRepository('AppBundle:Cities')->findOneBy(['ref' => $city->Ref]);
            if ($cityObject) {
                $cityObject->setActive(true);
            } else {
                $cityObject = new Cities();
                $cityObject->setCarriers($carrier);
                $cityObject->setName($city->DescriptionRu);
                $cityObject->setFullJson(json_encode($city));
                $cityObject->setActive(true);
            }
            $cityObject->setRef($city->Ref);
            $this->em->persist($cityObject);
            $this->em->flush($cityObject);
        }

        // Get and insert(update) new(old) Warehouses
        $cities = $this->em->getRepository('AppBundle:Cities')->findBy(['active' => 1]);

        foreach ($cities as $city) {
            $data = new Address_getWarehouses();
            $data->setCityRef($city->getRef());
            $warehouses = Address::getWarehouses($data);

            foreach ($warehouses->data as $warehouse) {
                $warehouseObject = $this->em->getRepository('AppBundle:Stores')->findOneBy(['ref' => $warehouse->Ref]);
                if ($warehouseObject) {
                    $warehouseObject->setActive(true);
                } else {
                    $warehouseObject = new Stores();
                    $warehouseObject->setCities($city);
                    $warehouseObject->setName($warehouse->DescriptionRu);
                    $warehouseObject->setFullJson(json_encode($warehouse));
                    $warehouseObject->setActive(true);
                }
                $warehouseObject->setRef($warehouse->Ref);

                $this->em->persist($warehouseObject);
                $this->em->flush($warehouseObject);
            }
        }

    }

    /**
     * @param $ref
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public function getInternetDocumentByRef($ref)
    {
        $data = new \NovaPoshta\MethodParameters\InternetDocument_getDocument();
        $data->setRef($ref);
        return InternetDocument::getDocument($data);
    }

    /**
     * @param $intDocNumber
     * @return \NovaPoshta\Models\DataContainerResponse
     */
    public function getInternetDocumentByIntDocNumber($intDocNumber)
    {
        $data = new \NovaPoshta\MethodParameters\InternetDocument_getDocumentList();
        $data->setIntDocNumber($intDocNumber);
        return InternetDocument::getDocumentList($data);
    }

    /**
     * @return array
     */
    public function getCities()
    {
        return Address::getCities()->data;
    }

    /**
     * @param DataContainerResponse $document
     * @return DataContainerResponse
     */
    public function getDocumentDeliveryDateByTtnDocument(DataContainerResponse $document)
    {
        $ttn = $document->data[0];

        $data = new \NovaPoshta\MethodParameters\InternetDocument_getDocumentDeliveryDate();
        $data->setDateTime($ttn->DateTime);
        $data->setCitySender($ttn->CitySenderRef);
        $data->setCityRecipient($ttn->CityRecipientRef);
        $data->setServiceType($ttn->ServiceTypeRef);
        return InternetDocument::getDocumentDeliveryDate($data);
    }

    /**
     * Initialize config
     *
     * @return null|object
     */
    public function initConfig()
    {
        $api = $this->em->getRepository('AppBundle:Novaposhta')
            ->findOneBy(['active' => 1]);
        Config::setApiKey($api->getApiKey());
        Config::setFormat(Config::FORMAT_JSONRPC2);
        Config::setLanguage(Config::LANGUAGE_RU);

        return $api;
    }

    /**
     * @param Carriers $carrier
     *
     * @return bool
     */
    public function isNovaPoshtaCarrier(Carriers $carrier)
    {
        return $carrier->getId() == 1;
    }

    /**
     * @param NovaposhtaSender $novaposhtaSender
     * @return DataContainerResponse
     */
    public function createContactPerson(NovaposhtaSender $novaposhtaSender){

        $this->initConfig();
        $sender = $this->getSenderCounterparties();
        $senderRef = $sender->Ref;

        $contactPerson = new ContactPerson();
        $contactPerson->setCounterpartyRef($senderRef);
        $contactPerson->setFirstName($novaposhtaSender->getFirstName());
        $contactPerson->setLastName($novaposhtaSender->getLastName());
        $contactPerson->setMiddleName($novaposhtaSender->getMiddleName());
        $contactPerson->setPhone($novaposhtaSender->getPhone());
        $contactPerson->setEmail($novaposhtaSender->getEmail());
        return $contactPerson->save();
    }

    /**
     * @param NovaposhtaSender $novaposhtaSender
     */
    public function removeContactPerson(NovaposhtaSender $novaposhtaSender){

        $this->initConfig();

        $contactPerson = new ContactPerson();
        $contactPerson->setRef($novaposhtaSender->getRef());
        $contactPerson->delete();
    }

    /**
     * @return mixed
     */
    protected function getSenderCounterparties(){

        $data = new MethodParameters();
        $data->CounterpartyProperty = 'Sender';
        $result = Counterparty::getCounterparties($data);
        return $result->data[0];
    }
}
