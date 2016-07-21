<?php

namespace AppAdminBundle\Admin;


use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class UsersAdmin extends BaseUserAdmin
{

    protected $userRole = 'a:0:{}';

    /**
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere('o.roles LIKE :role')->setParameter('role', '%'.$this->userRole.'%');
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
            ->add('name')
            ->add('surname')
            ->add('email')
            ->end()
            // .. more info
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper
            ->with('General')
            ->add('email')
            ->add('name')
            ->add('surname')
            ->add('plainPassword', 'text', ['required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))])
            ->end()
            // .. more info
        ;

        if (!$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->with('Management')
                ->add('roles', 'sonata_security_roles', array(
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false
                ))
                ->add('locked', null, array('required' => false))
                ->add('grayListFlag', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('name')
            ->add('surname')
            ->add('locked')
            ->add('email')
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email')
            ->add('name')
            ->add('surname')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('createTime')
        ;
    }
}
