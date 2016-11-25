<?php
/**
 * Created by PhpStorm.
 * User: anget
 * Date: 25.11.16
 * Time: 13:53
 */

namespace AppBundle\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;

class InvoiceTransformer
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param $orderObject
     * @return mixed
     */
    public function getInvoiceAction($orderObject){

        // ask the service for a Excel5
        $phpExcelObject = $this->container->get('phpexcel')->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("mmg")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");
        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('C7', 'Поставщик')
            ->setCellValue('D7', $this->getProviderName())
            ->setCellValue('C9', 'sss')
            ->setCellValue('D9', 'sss')
            ->setCellValue('C10', 'sss')
            ->setCellValue('D10', 'sss')
            ->setCellValue('G13', 'Товарный чек')
            ->setCellValue('B14', 'от')
            ->setCellValue('C14', new \DateTime())
            ->setCellValue('B16', 'N п/п')
            ->setCellValue('C16', 'Артикул')
            ->setCellValue('D16', 'Наименование')
            ->setCellValue('E16', 'Размер')
            ->setCellValue('F16', 'Цвет')
            ->setCellValue('G16', 'ед.Измерения')
            ->setCellValue('H16', 'Кол-во')
            ->setCellValue('I16', 'Цена')
            ->setCellValue('J16', 'Скидка')
            ->setCellValue('K16', 'Цена со скидкой')
            ->setCellValue('L16', 'Сумма');

        $this->outputSizes($phpExcelObject);
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'invoice.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @return mixed
     */
    private function getProviderName(){

        $arr = $this->container->get('options')->getParams('provider_name');
        return $arr['providerName']['value'];
    }

    /**
     * @param $phpExcelObject
     * @param $orderObject
     */
    private function outputSizes($phpExcelObject, $orderObject){

        $i = 17;
        foreach ($orderObject->getSizes() as $size){
            $phpExcelObject->getActiveSheet()
                ->setCellValue("B$i", $size->getQuantity())
                ->setCellValue('C'.$i, $size->getQuantity())
                ->setCellValue('D'.$i, $size->getQuantity())
                ->setCellValue('E'.$i, $size->getQuantity())
                ->setCellValue('F'.$i, $size->getQuantity())
                ->setCellValue('G'.$i, $size->getQuantity())
                ->setCellValue('H'.$i, $size->getQuantity());
            $i++;
        }
    }
}