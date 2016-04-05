<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        return $this->menuAddChildIterator('mainMenu', $factory, $options);
    }

    public function sideleftMenu(FactoryInterface $factory, array $options)
    {
        return $this->menuAddChildIterator('sideleftMenu', $factory, $options);
    }

    public function footerleftMenu(FactoryInterface $factory, array $options)
    {
        return $this->menuAddChildIterator('footerleftMenu', $factory, $options);
    }

    public function footerrightMenu(FactoryInterface $factory, array $options)
    {
        return $this->menuAddChildIterator('footerrightMenu', $factory, $options);
    }

    protected function menuAddChildIterator($menu_name, FactoryInterface $factory, $options)
    {
        $menu = $factory->createItem('root');

        $list = $this->container->get('doctrine')->getManager()
            ->createQueryBuilder()
            ->select('mi')
            ->from('AppBundle:MenuItems', 'mi')
            ->join('AppBundle:Menu', 'm', 'WITH', 'm.id = mi.menu')
            ->where('m.name = :name')
            ->orderBy('mi.priority', 'ASC')
            ->setParameter('name', $menu_name)
            ->getQuery()
            ->getResult();

        if ($list) {
            foreach ($list as $value) {
//                $onclick = "ga('send', 'event', '".$menu_name."', '".$value->getName()."', '".$value->getLink()."');";
//                $onclick .= "yaCounterXXXXXX.reachGoal('".$value->getName()."');";
                if ($value->getLinkType() == 'local') {
                    $link = explode('/', $value->getLink());
                    $route = $link[0];
                    if (isset($link[1])) {
                        array_shift($link);
                        $alias = implode('/', $link);
                        $menu->addChild($value->getName(), array(
                            'route' => $route,
                            'routeParameters' => array('alias' => $alias)
                        ));
//                            ->setLinkAttribute('onclick', $onclick);
                    } else {
                        $menu->addChild($value->getName(), array('route' => $route));
//                            ->setLinkAttribute('onclick', $onclick);
                    }
                } else {
                    $menu->addChild($value->getName(), array('uri' => $value->getLink()));
//                        ->setLinkAttribute('onclick', $onclick);
                }
            }
        }
        return $menu;
    }

    public function footercategoriesMenu(FactoryInterface $factory, $options)
    {
        $menu = $factory->createItem('root');
        try {
            $category_list = $this->container->get('entities')
                ->getAllActiveCategoriesForMenu();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $category_list = null;
        }
        if ($category_list) {
            foreach ($category_list as $menu_item) {
                if ($menu_item['parrent']['id'] == 1) {
                    $menu->addChild($menu_item['name'], array(
                        'route' => 'category',
                        'routeParameters' => array('category' => $menu_item['alias'])
                    ));
                }
            }
        }
        return $menu;
    }

    public function categoriesMenu(FactoryInterface $factory, $options)
    {
        $menu = $factory->createItem('root');

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_WHOLESALER')) {
            $menu->addChild('Все', array(
                'route' => 'category',
                'routeParameters' => array('category' => 'all')
            ));
        }

        try {
            $category_list = $this->container->get('entities')
                ->getAllActiveCategoriesForMenu();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $category_list = null;
        }
        if ($category_list) {
            $this->listCategoryRecursive($menu, $category_list, 1);
        }
        return $menu;
    }

    private function listCategoryRecursive($menu, $category_list, $parrent_id)
    {
        foreach ($category_list as $menu_item) {
            if ($menu_item['parrent']['id'] == $parrent_id) {
                $menu->addChild($menu_item['name'], array(
                    'route' => 'category',
                    'routeParameters' => array('category' => $menu_item['alias'])
                ));
                foreach ($category_list as $cat_list) {
                    if ($cat_list['parrent']['id'] == $menu_item['id']) {
                        $menu[$menu_item['name']]->setLabel($menu_item['name'])
                            ->setAttribute('class', 'has-sub-nav')
                            ->setChildrenAttribute('class', 'sub-nav');
                        $this->listCategoryRecursive($menu[$menu_item['name']], $category_list, $menu_item['id']);
                    }
                }
            }
        }
    }

}
