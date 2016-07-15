<?php

namespace AppAdminBundle\Transformer;

use AppAdminBundle\Admin\ImportAdmin;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractTransformer
{
    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    protected $transformedData;

    /**
     * @var
     */
    protected $transformFlag;

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->transformFlag = true;
        $this->data = $data;
    }

    public function transformedData()
    {
        if ($this->transformedData && !$this->transformFlag) {
            return $this->transformedData;
        }

        $this->transformFlag = false;
        $this->transformation();
        
        return $this->transformedData;
    }

    /**
     * Transform data
     * 
     * @return mixed
     */
    abstract protected function transformation();
}
