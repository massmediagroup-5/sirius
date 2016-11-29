<?php
/**
 * Created by PhpStorm.
 * User: anget
 * Date: 25.11.16
 * Time: 13:53
 */

namespace AppBundle\Services;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
    public function createInvoice($orderObject){

        // ask the service for a Excel5
        $phpExcel = $this->container->get('phpexcel');
        $phpExcelObject = $phpExcel->createPHPExcelObject();

        $phpExcelObject->getProperties()->setCreator("mmg")
            ->setTitle("Office 2005 XLSX Test Document")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B13', 'Товарный чек № '.$orderObject->getId())
            ->setCellValue('C7', 'Поставщик')
            ->setCellValue('D7', $this->getProviderName())
            ->setCellValue('C9', 'Получатель')
            ->setCellValue('D9', $orderObject->getFio())
            ->setCellValue('C10', 'Заказ')
            ->setCellValue('D10', '№'.$orderObject->getId())
            ->setCellValue('B14', 'от')
            ->setCellValue('C14', new \DateTime())
            ->setCellValue('B16', '№ п/п')
            ->setCellValue('C16', 'Артикул')
            ->setCellValue('D16', 'Наименование')
            ->setCellValue('E16', 'Размер')
            ->setCellValue('F16', 'Цвет')
            ->setCellValue('G16', 'ед.Измерения')
            ->setCellValue('H16', 'Кол-во')
            ->setCellValue('I16', 'Цена')
            ->setCellValue('J16', 'Скидка')
            ->setCellValue('K16', 'Цена со скидкой')
            ->setCellValue('L16', 'Сумма')

            ->setCellValue('L'.($this->getRowCountMainTable($orderObject)+16), $orderObject->getDiscountedTotalPrice())

            ->setCellValue('I'.($this->getRowCountMainTable($orderObject)+17), 'Скидка')
            ->setCellValue('K'.($this->getRowCountMainTable($orderObject)+17), $this->getLoyalityDiscount($orderObject) ? $this->getLoyalityDiscount($orderObject).' %' : '')
            ->setCellValue('L'.($this->getRowCountMainTable($orderObject)+17), $orderObject->getLoyalityDiscount($orderObject))

            ->setCellValue('I'.($this->getRowCountMainTable($orderObject)+18), 'Оплачено бонусами')
            ->setCellValue('L'.($this->getRowCountMainTable($orderObject)+18), $orderObject->getBonuses())

            ->setCellValue('I'.($this->getRowCountMainTable($orderObject)+19), 'Сумма к оплате')
            ->setCellValue('L'.($this->getRowCountMainTable($orderObject)+19), $this->getFinallyPrice($orderObject))

            ->setCellValue('B'.($this->getRowCountMainTable($orderObject)+21), 'Всего к оплате')
            ->setCellValue('B'.($this->getRowCountMainTable($orderObject)+21), 'Сумма прописью ...')

            ->setCellValue('C'.($this->getRowCountMainTable($orderObject)+24), 'Отгрузил')
            ->setCellValue('E'.($this->getRowCountMainTable($orderObject)+24), 'Подпись');

        $rowCount = $this->outputSizes($phpExcelObject, $orderObject) - 1;

        //init styles
        $phpExcelObject->getActiveSheet()->getStyle("B16:L$rowCount")->applyFromArray($this->getBorder());
        $phpExcelObject->getActiveSheet()->getStyle('L'.($this->getRowCountMainTable($orderObject)+16))->applyFromArray($this->getBorder());
        $phpExcelObject->getActiveSheet()->getStyle('I'.($this->getRowCountMainTable($orderObject)+17).':L'.($this->getRowCountMainTable($orderObject)+19))->applyFromArray($this->getBorder());

        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('L')->setWidth(25);

        $phpExcelObject->getActiveSheet()->getStyle('B'.($this->getRowCountMainTable($orderObject)+21))->getFont()->applyFromArray(
            [
                'bold' => true
            ]
            );

        $phpExcelObject->getActiveSheet()->getStyle('C7:C10')->applyFromArray(
            [
                'font' => [
                    'underline' => \PHPExcel_Style_Font::UNDERLINE_SINGLE,
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'wrap'       => true
                ],
            ]
        );

        for ($i = 1; $i <= 200; $i++){

            $phpExcelObject->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
        }

        $phpExcelObject->getActiveSheet()->getRowDimension('13')->setRowHeight(30);
        $phpExcelObject->getActiveSheet()->mergeCells('B13:L13');
        $phpExcelObject->getActiveSheet()->mergeCells('I'.($this->getRowCountMainTable($orderObject)+17).':J'.($this->getRowCountMainTable($orderObject)+17));
        $phpExcelObject->getActiveSheet()->mergeCells('I'.($this->getRowCountMainTable($orderObject)+18).':K'.($this->getRowCountMainTable($orderObject)+18));
        $phpExcelObject->getActiveSheet()->mergeCells('I'.($this->getRowCountMainTable($orderObject)+19).':K'.($this->getRowCountMainTable($orderObject)+19));
        $phpExcelObject->getActiveSheet()->mergeCells('B'.($this->getRowCountMainTable($orderObject)+21).':C'.($this->getRowCountMainTable($orderObject)+21));
        $phpExcelObject->getActiveSheet()->mergeCells('D'.($this->getRowCountMainTable($orderObject)+21).':G'.($this->getRowCountMainTable($orderObject)+21));
        $phpExcelObject->getActiveSheet()->getStyle('B13:L13')->applyFromArray(
            [
                'font' => [
                    'size' => '16'
                ],
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'wrap'       => true
                ],
            ]
        );

        $phpExcelObject->getActiveSheet()->getStyle('B16:L16')->getAlignment()->applyFromArray(
            [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'wrap'       => true
            ]
        );

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $phpExcel->createWriter($phpExcelObject, 'Excel5');
        // create the response
        $response = $phpExcel->createStreamedResponse($writer);
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
     * @return int
     */
    private function outputSizes($phpExcelObject, $orderObject){

        $i = 17;
        foreach ($orderObject->getSizes() as $size){

                $phpExcelObject->getActiveSheet()
                ->setCellValue("B$i", $i)
                ->setCellValue("C$i", $size->getSize()->getModel()->getProducts()->getArticle())
                ->setCellValue("D$i", $size->getSize()->getModel()->getProducts()->getName())
                ->setCellValue("E$i", $size->getSize()->getSize())
                ->setCellValue("F$i", $size->getSize()->getModel()->getProductColors()->getName())
                ->setCellValue("G$i", 'TODO')
                ->setCellValue("H$i", $size->getQuantity())
                ->setCellValue("I$i", $size->getTotalPricePerItem())
                ->setCellValue("J$i", $this->getDiscount($size) ? $this->getDiscount($size).' %' : '')
                ->setCellValue("K$i", $size->getDiscountedTotalPricePerItem())
                ->setCellValue("L$i", $size->getDiscountedTotalPrice());
            $i++;
        }
        return $i;
    }

    /**
     * @return array
     */
    private function getBorder(){

        return [
            'borders' => [
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    'color' => [
                    'rgb' => ''
                    ]
                ]
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'wrap'       => true
            ],
        ];
    }

    /**
     * @param $orderObject
     * @return int
     */
    private function getRowCountMainTable($orderObject)
    {
        $rowCount = 1;
        foreach ($orderObject->getSizes() as $size) {

            $rowCount++;
        }
        return $rowCount;
    }

    /**
     * @param $size
     * @return bool|float|int
     */
    private function getDiscount($size){

        if ($size->getTotalPrice() != $size->getDiscountedTotalPrice()){

            $diff = $size->getTotalPrice() - $size->getDiscountedTotalPrice();
            $res = number_format((($diff / $size->getTotalPrice()) * 100), 2, '.', '');
            return $res;
        }
        return false;
    }

    /**
     * @param $orderObject
     * @return float|int
     */
    private function getLoyalityDiscount($orderObject){

        return ($orderObject->getLoyalityDiscount() / $orderObject->getDiscountedTotalPrice()) * 100;
    }

    private function getFinallyPrice($orderObject){

        return $orderObject->getDiscountedTotalPrice() - $orderObject->getLoyalityDiscount() - $orderObject->getBonuses();
    }
}