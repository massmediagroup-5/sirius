<?php

namespace AppAdminBundle;

use Sonata\CoreBundle\Form\FormHelper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $this->registerFormMapping();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->registerFormMapping();
    }

    /**
     * Register form mapping information
     */
    public function registerFormMapping()
    {
        FormHelper::registerFormTypeMapping([
            'sonata_security_roles' => 'AppAdminBundle\Form\Type\SecurityRolesType'
        ]);
    }
}
