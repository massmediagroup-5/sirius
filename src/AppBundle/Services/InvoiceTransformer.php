<?php
/**
 * Created by PhpStorm.
 * User: anget
 * Date: 25.11.16
 * Time: 13:53
 */

namespace AppBundle\Services;


use AppAdminBundle\Services\WholesalerCart;
use AppBundle\Entity\Orders;
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
    public function createInvoice(Orders $orderObject)
    {
        // ask the service for a Excel5
        $phpExcel = $this->container->get('phpexcel');
        $phpExcelObject = $phpExcel->createPHPExcelObject();
        $sizesCount = $orderObject->getSizes()->count();

        $phpExcelObject->getProperties()->setCreator("mmg")
            ->setTitle("Office 2005 XLSX Test Document");

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('B13', 'Товарный чек № ' . $orderObject->getId())
            ->setCellValue('C7', 'Поставщик')
            ->setCellValue('D7', $this->getProviderName())
            ->setCellValue('C9', 'Получатель')
            ->setCellValue('D9', $orderObject->getFio())
            ->setCellValue('C10', 'Заказ')
            ->setCellValue('D10', '№' . $orderObject->getId())
            ->setCellValue('B14', 'от')
            ->setCellValue('C14', (new \DateTime())->format('d.m.Y'))
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
            ->setCellValue('L' . ($sizesCount + 16), $orderObject->getDiscountedTotalPrice())
            ->setCellValue('I' . ($sizesCount + 17), 'Скидка')
            ->setCellValue('K' . ($sizesCount + 17),
                $this->getLoyalityDiscount($orderObject) ? $this->getLoyalityDiscount($orderObject) . ' %' : '')
            ->setCellValue('L' . ($sizesCount + 17), $orderObject->getLoyalityDiscount($orderObject))
            ->setCellValue('I' . ($sizesCount + 18), 'Оплачено бонусами')
            ->setCellValue('L' . ($sizesCount + 18), $orderObject->getBonuses())
            ->setCellValue('I' . ($sizesCount + 19), 'Сумма к оплате')
            ->setCellValue('L' . ($sizesCount + 19), $this->getFinallyPrice($orderObject))
            ->setCellValue('B' . ($sizesCount + 21), 'Всего к оплате:')
            ->setCellValue('D' . ($sizesCount + 21), $this->getFinallyPrice($orderObject))
            ->setCellValue('C' . ($sizesCount + 24), 'Отгрузил')
            ->setCellValue('E' . ($sizesCount + 24), 'Подпись');

        if ($orderObject->getUsers() && $orderObject->getUsers()->hasRole('ROLE_WHOLESALER')) {
            $this->outputWholesalerSizes($phpExcelObject, $orderObject);
        } else {
            $this->outputSizes($phpExcelObject, $orderObject);
        }
        $rowCount = 17 + $sizesCount - 1;

        //init styles
        $phpExcelObject->getActiveSheet()->getStyle("B16:L$rowCount")->applyFromArray($this->getBorder());
        $phpExcelObject
            ->getActiveSheet()
            ->getStyle('L' . ($sizesCount + 16))
            ->applyFromArray($this->getBorder());
        $phpExcelObject
            ->getActiveSheet()
            ->getStyle('I' . ($sizesCount + 17) . ':L' . ($sizesCount + 19))
            ->applyFromArray($this->getBorder());

        $phpExcelObject->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('J')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('F')->setWidth(12);
        $phpExcelObject->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('K')->setWidth(15);
        $phpExcelObject->getActiveSheet()->getColumnDimension('L')->setWidth(25);

        $phpExcelObject
            ->getActiveSheet()
            ->getStyle('B' . ($sizesCount + 21))
            ->getFont()
            ->applyFromArray(['bold' => true]);

        $phpExcelObject->getActiveSheet()->getStyle('C7:C10')->applyFromArray(
            [
                'font' => [
                    'underline' => \PHPExcel_Style_Font::UNDERLINE_SINGLE,
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'wrap' => true
                ],
            ]
        );

        $phpExcelObject->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);

        $phpExcelObject->getActiveSheet()->getRowDimension('13')->setRowHeight(30);
        $phpExcelObject->getActiveSheet()->mergeCells('B13:L13');
        $phpExcelObject->getActiveSheet()->mergeCells('I' . ($sizesCount + 17) . ':J' . ($sizesCount + 17));
        $phpExcelObject->getActiveSheet()->mergeCells('I' . ($sizesCount + 18) . ':K' . ($sizesCount + 18));
        $phpExcelObject->getActiveSheet()->mergeCells('I' . ($sizesCount + 19) . ':K' . ($sizesCount + 19));
        $phpExcelObject->getActiveSheet()->mergeCells('B' . ($sizesCount + 21) . ':C' . ($sizesCount + 21));
        $phpExcelObject->getActiveSheet()->getStyle('B13:L13')->applyFromArray(
            [
                'font' => [
                    'size' => '16'
                ],
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'wrap' => true
                ],
            ]
        );

        $phpExcelObject->getActiveSheet()->getStyle('B16:L16')->getAlignment()->applyFromArray(
            [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'wrap' => true
            ]
        );

        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);

        // create the writer
        $writer = $phpExcel->createWriter($phpExcelObject, 'Excel2007');
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
    private function getProviderName()
    {
        return $this->container->get('options')->getParamValue('provider_name');
    }

    /**
     * @param $phpExcelObject
     * @param $orderObject
     */
    private function outputSizes($phpExcelObject, $orderObject)
    {
        $i = 17;

        foreach ($orderObject->getSizes() as $size) {

            $phpExcelObject->getActiveSheet()
                ->setCellValue("B$i", $i)
                ->setCellValue("C$i", $size->getSize()->getModel()->getProducts()->getArticle())
                ->setCellValue("D$i", $size->getSize()->getModel()->getProducts()->getName())
                ->setCellValue("E$i", $size->getSize()->getSize())
                ->setCellValue("F$i", $size->getSize()->getModel()->getProductColors()->getName())
                ->setCellValue("G$i", 'TODO')
                ->setCellValue("H$i", $size->getQuantity())
                ->setCellValue("I$i", $size->getTotalPricePerItem())
                ->setCellValue("J$i", $this->getDiscount($size) ? $this->getDiscount($size) . ' %' : '')
                ->setCellValue("K$i", $size->getDiscountedTotalPricePerItem())
                ->setCellValue("L$i", $size->getDiscountedTotalPrice());
            $i++;
        }
    }

    /**
     * @param $phpExcelObject
     * @param $orderObject
     */
    private function outputWholesalerSizes($phpExcelObject, $orderObject)
    {
        $i = 17;
        /** @var WholesalerCart $cart */
        $cart = $this->container->get('admin.wholesaler_cart');
        $cart->setOrder($orderObject);

        foreach ($cart->getModels() as $model) {
            $packagesCount = $cart->getPackagesCount($model);

            if ($packagesCount) {
                $sizes = array_map(function ($size) {
                    return $size->getSize()->getSize();
                }, $cart->getModelSizes($model));

                $sizesNames = implode(', ', $sizes);
                $discount = $cart->getModelPackageDiscount($model);

                $phpExcelObject->getActiveSheet()
                    ->setCellValue("B$i", $i)
                    ->setCellValue("C$i", $model->getProducts()->getArticle())
                    ->setCellValue("D$i", $model->getProducts()->getName())
                    ->setCellValue("E$i", $sizesNames)
                    ->setCellValue("F$i", $model->getProductColors()->getName())
                    ->setCellValue("G$i", 'пач.')
                    ->setCellValue("H$i", $packagesCount)
                    ->setCellValue("I$i", $cart->getModelPackagePricePerItem($model))
                    ->setCellValue("J$i", $discount ? $discount . ' %' : '')
                    ->setCellValue("K$i", $cart->getModelDiscountedPackagePricePerItem($model))
                    ->setCellValue("L$i", $cart->getModelDiscountedPackagesPrice($model));
                $i++;
            }
            foreach ($cart->getSingleSizes($model) as $size) {
                $discount = $size['entity']->getTotalPricePerItem() - $size['entity']->getDiscountedTotalPricePerItem();
                $discountPrc = $discount / $size['entity']->getTotalPricePerItem() * 100;
                $phpExcelObject->getActiveSheet()
                    ->setCellValue("B$i", $i)
                    ->setCellValue("C$i", $model->getProducts()->getArticle())
                    ->setCellValue("D$i", $model->getProducts()->getName())
                    ->setCellValue("E$i", $size['entity']->getSize()->getSize())
                    ->setCellValue("F$i", $model->getProductColors()->getName())
                    ->setCellValue("G$i", 'пач.')
                    ->setCellValue("H$i", $size['quantity'])
                    ->setCellValue("I$i", $size['entity']->getTotalPricePerItem())
                    ->setCellValue("J$i", $discountPrc ? $discountPrc . ' %' : '')
                    ->setCellValue("K$i", $size['entity']->getDiscountedTotalPricePerItem())
                    ->setCellValue("L$i", $size['entity']->getDiscountedTotalPricePerItem() * $size['quantity']);
                $i++;
            }
        }
    }

    /**
     * @return array
     */
    private function getBorder()
    {

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
                'wrap' => true
            ],
        ];
    }

    /**
     * @param $size
     * @return bool|float|int
     */
    private function getDiscount($size)
    {

        if ($size->getTotalPrice() != $size->getDiscountedTotalPrice()) {

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
    private function getLoyalityDiscount($orderObject)
    {

        return ($orderObject->getLoyalityDiscount() / $orderObject->getDiscountedTotalPrice()) * 100;
    }

    private function getFinallyPrice($orderObject)
    {

        return $orderObject->getDiscountedTotalPrice() - $orderObject->getLoyalityDiscount() - $orderObject->getBonuses();
    }
}