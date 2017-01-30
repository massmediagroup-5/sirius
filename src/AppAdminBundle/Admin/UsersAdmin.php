<?php

namespace AppAdminBundle\Admin;


use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use AppBundle\Entity\Users;
use Symfony\Component\Form\CallbackTransformer;

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

        if ($this->userRole) {
            $query->andWhere('o.roles LIKE :role')->setParameter('role', "%$this->userRole%");
        } else {
            $query->andWhere('o.roles NOT LIKE :role_wholesaler')
                ->andWhere('o.roles NOT LIKE :role_admin')
                ->setParameter('role_wholesaler', '%ROLE_WHOLESALER%')
                ->setParameter('role_admin', '%ROLE_ADMIN%');
        }
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
        $this->setTemplate('edit', 'AppAdminBundle:admin:user_edit.html.twig');

        $formMapper
            ->tab('Общее')
            ->with('Общее')
            ->add('email')
            ->add('phone')
            ->add('name')
            ->add('surname')
            ->add('plainPassword', 'text', [
                'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
            ])
            ->end();

        $formMapper
            ->with('Management')
            ->add('roles', 'sonata_security_roles', [
                'expanded' => true,
                'multiple' => true,
                'required' => false
            ])
            ->add('locked', null, ['required' => false])
            ->add('enabled', null, ['required' => false])
            ->end();

        $formMapper
            ->end()
            ->tab('Заказы', ['tab_template' => 'AppAdminBundle:admin:users/orders_tab.html.twig'])
            ->end();

        $formMapper
            ->tab('Скидки')
            ->with('Скидки')
            ->add('discount', null, ['required' => false])
            ->add('bonuses', null, ['required' => false]);

        if ($this->getSubject()->hasRole('ROLE_WHOLESALER')) {
            $loyaltyProgram = $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:WholesalerLoyaltyProgram')
                ->firstBySum($this->getSubject()->getTotalSpent());

            $formMapper->add('loyaltyDiscount', 'text', [
                'mapped' => false,
                'data' => $loyaltyProgram ? $loyaltyProgram->getDiscount() : 0,
                'disabled' => true
            ]);
        }

        $formMapper->end();

        $formMapper->getFormBuilder()
            ->addModelTransformer(new CallbackTransformer(
                function ($user) {
                    return $user;
                },
                function ($user) {
                    $user->setUsername($user->getEmail());
                    return $user;
                }
            ));;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('name', 'doctrine_orm_callback',
                [
                    'label'       => 'Имя',
                    'show_filter' => true,
                    'callback'    => function ($queryBuilder, $alias, $field, $value) {
                        if ( ! $value['value']) {
                            return false;
                        }
                        $queryBuilder
                            ->andWhere($alias . '.name LIKE :value')
                            ->setParameter('value', '%' . $value['value']->getName() . '%');

                        return true;
                    },
                ],
                'entity',
                [
                    'class'         => 'AppBundle:Users',
                    'property'      => 'name',
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('o');
                            $qb->select('o')->groupBy('o.name');

                            return $qb;
                        }
                ]
            )
            ->add('surname', 'doctrine_orm_callback',
                [
                    'label'       => 'Фамилия',
                    'show_filter' => true,
                    'callback'    => function ($queryBuilder, $alias, $field, $value) {
                        if ( ! $value['value']) {
                            return false;
                        }
                        $queryBuilder
                            ->andWhere($alias . '.surname LIKE :value')
                            ->setParameter('value', '%' . $value['value']->getSurname() . '%');

                        return true;
                    },
                ],
                'entity',
                [
                    'class'         => 'AppBundle:Users',
                    'property'      => 'surname',
                    'query_builder' =>
                        function ($er) {
                            $qb = $er->createQueryBuilder('o');
                            $qb->select('o')->groupBy('o.surname');

                            return $qb;
                        }
                ]
            )
            ->add('locked', 'doctrine_orm_choice', [],
                'choice',
                [
                    'choices' => [
                        '1' => 'Да',
                        '0' => 'Нет',
                    ]
                ]
            )
            ->add('email');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email', null, [
                'template' => 'AppAdminBundle:list:user_email.html.twig'
            ])
            ->add('name')
            ->add('surname')
            ->add('enabled', null, ['editable' => true])
            ->add('locked', null, ['editable' => true])
            ->add('createTime');
    }
}
