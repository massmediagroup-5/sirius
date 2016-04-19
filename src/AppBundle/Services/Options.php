<?php

namespace AppBundle\Services;

use Doctrine\ORM\EntityManager;

/**
 * Class: Options
 *
 */
class Options
{

    /**
     * params
     *
     * @var mixed
     */
    private $params;

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
     * getParams
     *
     * @return array
     */
    public function getParams() {
        if (empty($this->params)) {
            $params = $this->em->getRepository('AppBundle:SiteParams')->findAll();
            foreach($params as $value){
                $this->params[$value->getParamName()] = array(
                    'value' => $value->getParamValue(),
                    'active' => $value->getActive()
                );
            }
        }
        return $this->params;
    }

}
