<?php

namespace AppBundle\FileReader;

use AppBundle\FileReader\Transformer\TransformerInterface;
use Illuminate\Support\Str;

/**
 * Read XLS files
 *
 * @author zimm
 */
class XlsReader extends AbstractReader
{

    /**
     * @var \PHPExcel
     */
    protected $objPHPExcel;

    /**
     * @var \PHPExcel_Worksheet
     */
    protected $sheet;

    /**
     * Headers contain list of fields keys
     *
     * @var array
     */
    protected $headers = [];

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * Cache rows count
     *
     * @var int
     */
    protected $rowsCount;

    /**
     * @param string $file File path
     * @param TransformerInterface $transformer
     * @throws \PHPExcel_Exception
     */
    public function __construct($file, TransformerInterface $transformer = null)
    {
        $this->objPHPExcel = \PHPExcel_IOFactory::load($file);
        $this->sheet = $this->objPHPExcel->getSheet();
        $this->transformer = $transformer;
        $this->setHeaders();
    }

    /**
     * Return row data formatted to array
     *
     * @param $index
     * @return array
     * @throws \PHPExcel_Exception
     */
    public function getDataByIndex($index)
    {
        // Start from 1 and skip headers
        $index += 2;

        // Get row with data
        $cellIterator = $this->getRow($index)->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        $data = [];
        foreach ($this->headers as $letter => $field) {
            $cell = $this->sheet->getCell($letter . $index);
            $data[$field] = $this->prepareValue($cell->getFormattedValue());
        }

        if ($this->transformer) {
            $data = $this->transformer->transform($data);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getFields()
    {
        return array_values($this->headers);
    }

    /**
     * Get sheet row by index
     * @param $rowNumber
     * @return \PHPExcel_Worksheet_Row
     */
    public function getRow($rowNumber)
    {
        return $this->sheet->getRowIterator($rowNumber)->current();
    }

    /**
     * Implements Countable
     *
     * @return int
     */
    public function count()
    {
        return $this->getRowsCount();
    }

    /**
     * Set headers
     */
    protected function setHeaders()
    {
        $cellIterator = $this->getRow(1)->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);

        $this->headers = [];
        foreach ($cellIterator as $index => $cell) {
            // Camelize header name
            $this->headers[$index] = $cell->getValue();
        }
    }

    /**
     * Get highest column and count rows in it. Subtract header row.
     *
     * @return int
     * @throws \PHPExcel_Exception
     */
    protected function getRowsCount()
    {
        return $this->rowsCount === null ? $this->rowsCount = $this->sheet->getHighestDataRow() - 1 : $this->rowsCount;
    }
}