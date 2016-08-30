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
        'Модель' => 'products.name',
        'Активность' => 'products.active',
        'Артикул' => 'products.article',
        'Категория' => 'products.baseCategory.name',
        'Цена модели' => 'products.price',
        'Оптовая цена модели' => 'products.wholesalePrice',
        'Цвет' => 'color.name',
        'Цвет отдели' => 'decorationColor.name',
        'Алиас' => 'model.alias',
        'Цена продукта' => 'model.price',
        'Оптовая цена продукта' => 'model.wholesalePrice',
        'Продукт опубликован' => 'model.published',
        'Количество модели' => 'model.quantity',
        'Размер' => 'size',
        'Цена размера' => 'price',
        'Оптовая цена размера' => 'wholesalePrice',
        'Предзаказ' => 'preOrderFlag',
        'Количество размера' => 'quantity',
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
