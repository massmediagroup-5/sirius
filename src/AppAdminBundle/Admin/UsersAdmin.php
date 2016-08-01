<?php

namespace AppAdminBundle\Admin;


use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity\Users;

class UsersAdmin extends BaseUserAdmin
{

    protected $userRole;

    /**
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        $role = $this->userRole ?: 'a:0:{}';

        $query->andWhere('o.roles LIKE :role')->setParameter('role', "%$role%");
        return $query;
    }

    /**
     * @inheritdoc
     */
    public function getNewInstance()
    {
        /** @var Users $object */
        $object = parent::getNewInstance();

        if ($this->userRole) {
            $object->setRoles([$this->userRole]);
        }

        return $object;
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
            ->end()// .. more info
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
            ->add('plainPassword', 'text', [
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
            ])
            ->end();

        if (!$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->with('Management')
                ->add('roles', 'sonata_security_roles', [
                    'expanded' => true,
                    'multiple' => true,
                    'required' => false
                ])
                ->add('locked', null, ['required' => false])
                ->add('grayListFlag', null, ['required' => false])
                ->add('discount', null, ['required' => false])
                ->add('enabled', null, ['required' => false])
                ->end();
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
            ->add('email');
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
            ->add('enabled', null, ['editable' => true])
            ->add('locked', null, ['editable' => true])
            ->add('createTime');
    }
}
