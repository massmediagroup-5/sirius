<?php

namespace AppAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class StoresAutoCompleteType
 * @package AppAdminBundle\Form\Type
 */
class SonataStoresListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sonata_stores_list';
    }
}