<?php
/**
 * Created by PhpStorm.
 * User: TaPTaK
 * Date: 15.06.2016
 * Time: 9:40
 */

namespace AppBundle\Services;

use AppBundle\AppBundle;
use AppBundle\Entity\Cities;
use AppBundle\Entity\Stores;
use Doctrine\ORM\EntityManager;
use NovaPoshta\ApiModels\Address;
use NovaPoshta\MethodParameters\Address_getWarehouses;
use NovaPoshta\MethodParameters\Address_getCities;

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
		$carrier = $this->em->getRepository('AppBundle:Carriers')->findOneBy(['name'=>'Новая почта']);

		// Get and insert new Cities
		$cities = Address::getCities();
		foreach($cities->data as $city)
		{
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

		foreach($cities as $city)
		{
			$data = new Address_getWarehouses();
			$data->setCityRef($city->getRef());
			$warehouses = Address::getWarehouses($data);

			foreach($warehouses->data as $warehouse)
			{
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
		        ->set('c.active', '0')
		        ->getQuery()
		->execute();
		$this->em->createQueryBuilder()
			->update('AppBundle:Stores', 's')
		        ->set('s.active', '0')
		        ->getQuery()
		->execute();

		$carrier = $this->em->getRepository('AppBundle:Carriers')->findOneBy(['name'=>'Новая почта']);

		// Get and insert(update) new(old) Cities
		$cities = Address::getCities();
		foreach($cities->data as $city)
		{
			$cityObject = $this->em->getRepository('AppBundle:Cities')->findOneBy(['ref'=>$city->Ref]);
			if($cityObject) {
				$cityObject->setActive(true);
			} else {
				$cityObject = new Cities();
				$cityObject->setCarriers($carrier);
				$cityObject->setName($city->DescriptionRu);
				$cityObject->setRef($city->Ref);
				$cityObject->setFullJson(json_encode($city));
				$cityObject->setActive(true);
			}
			$this->em->persist($cityObject);
			$this->em->flush($cityObject);
		}

		// Get and insert(update) new(old) Warehouses
		$cities = $this->em->getRepository('AppBundle:Cities')->findBy(['active'=>1]);

		foreach($cities as $city)
		{
			$data = new Address_getWarehouses();
			$data->setCityRef($city->getRef());
			$warehouses = Address::getWarehouses($data);

			foreach($warehouses->data as $warehouse)
			{
				$warehouseObject = $this->em->getRepository('AppBundle:Stores')->findOneBy(['ref'=>$warehouse->Ref]);
				if($warehouseObject) {
					$warehouseObject->setActive(true);
				} else {
					$warehouseObject = new Stores();
					$warehouseObject->setCities($city);
					$warehouseObject->setName($warehouse->DescriptionRu);
					$warehouseObject->setRef($warehouse->Ref);
					$warehouseObject->setFullJson(json_encode($warehouse));
					$warehouseObject->setActive(true);
				}

				$this->em->persist($warehouseObject);
				$this->em->flush($warehouseObject);
			}
		}

	}

}
