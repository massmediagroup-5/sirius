<?php

namespace AppImportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class: ImportController
 *
 * @see Controller
 */
class ImportController extends Controller
{

    /**
     * ImportingAction
     *
     * @return mixed
     */
    public function ImportingAction()
    {
        // check import
        if(!flock(fopen("import/.import.lock", 'w'), LOCK_EX | LOCK_NB))
          die("Already runninng\n");
        // ---
        $importFileName = 'import/import.xls';
        $productModels = $data = $this->get('import')
            ->setVendor('yugcontract')
            ->setWorksheets(array(
                //'КОМПЬЮТЕРНАЯ ТЕХНИКА',
                'БЫТОВАЯ ТЕХНИКА'
            ))
            ->setTitleLine(10)
            ->setTitleSKU('Артикул')
            ->setTitleName('Наименование рус.')
            ->setTitleProductName('Наименование англ.')
            ->setTitlePrice('Цена на сайт')
            ->import($importFileName)
            ;
        //dump($productModels);
        $this->get('updateAll')
            ->unpublishedProductModelsExcept($productModels);
        $this->get('import')
            ->setVendor('yugcontract')
            ->putNotParsed($productModels);

        return $this->render('AppImportBundle:Default:import.html.twig',
            array('data' => $data));
    }

    /**
     * CharacteristicsImportAction
     *
     * @return mixed
     */
    public function CharacteristicsImportAction()
    {
        // check import
        if(!flock(fopen("import/.import.lock", 'w'), LOCK_EX | LOCK_NB))
          die("Already runninng\n");
        // ---
        $data = $this->get('importCharacteristics')
            ->setTabelCharacteristics('specnames')
            ->setTabelCharacteristicValues('mytex.test')
            ->setTabelModels('models')
            ->setTabelNotParsed('not_parsed')
            ->setCharacteristicValuesName('naimenovanie')
            ->setImgUrl('foto')
            ->setCategory('category')
            ->setModelNameInChV('URL')
            ->setModelNameInModel('model')
            ->setModelArticulInModel('artikyl')
            ->setNotCharacteristics('speclist')
            ->setNumerator('id')
            ->setColumnColor('TSvet')
            ->import()
            ;
        $this->get('checkRelationship')
            ->checkVsProducts();
        return $this->render('AppImportBundle:Default:import.html.twig',
            array('data' => $data));
    }

}
