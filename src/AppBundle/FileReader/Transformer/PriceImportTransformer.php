<?php

namespace AppBundle\FileReader\Transformer;


/**
 * Class DealXlsTransformer
 * @package AppBundle\Transformer
 * @author zimm
 */
class PriceImportTransformer implements TransformerInterface
{

    /**
     * Map import fields names to local fields names
     *
     * @var array
     */
    protected $convertFieldsMap = [
        'Категория' => 'products.baseCategory.name',
        'Модель' => 'products.name',
        'Цвет' => 'color.name',
        'Цвет отдели' => 'decorationColor.name',
        'Артикул' => 'products.article',
        'Размер' => 'size',
        'Цена размера' => 'price',
        'Оптовая цена размера' => 'wholesalePrice',
        'Предзаказ' => 'preOrderFlag',
        'Количество размера' => 'quantity',
        'Активность' => 'products.active',
        'Продукт опубликован' => 'model.published',
    ];

    /**
     * @inheritdoc
     */
    public function transform($dealData)
    {
        $converted = [];

        foreach ($dealData as $originalKey => $value) {
            $value = trim($value);

            // Remove repeated spaces
            $value = preg_replace('/\s+/', ' ', $value);

            if (isset($this->convertFieldsMap[$originalKey])) {
                $converted[$this->convertFieldsMap[$originalKey]] = $value;
            }
        }

        return $converted;
    }

}
