<?php

namespace AppAdminBundle\Transformer;

/**
 * Class ImportTransformer
 * @package AppAdminBundle\Transformer
 */
class ImportTransformer extends AbstractTransformer
{
    /**
     * @inheritdoc
     */
    protected function transformation()
    {
        $this->transformedData = $this->data;

        foreach ($this->transformedData as $key => $value) {
            $value = trim($value);

            // Remove repeated spaces
            $value = preg_replace('/\s+/', ' ', $value);

            $this->transformedData[$key] = $value;
        }

        $this->transformedData['sizes'] = array_map(function ($size) {
            return trim($size);
        }, explode('/', $this->transformedData['sizes']));
    }
}
