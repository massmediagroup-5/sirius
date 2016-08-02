<?php

namespace AppBundle\Listener;


use AppBundle\Event\ShareGroupFiltersUpdated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EntityEventsSubscriber
 * @package AppBundle\Listener
 */
class ShareGroupListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface $lastUrls
     */
    protected $container;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'app.share_group_filters_updated' => 'onShareGroupFiltersUpdated',
        ];
    }

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ShareGroupFiltersUpdated $event
     */
    public function onShareGroupFiltersUpdated(ShareGroupFiltersUpdated $event)
    {
        $this->container->get('share')->updateShareGroupSizes($event->getSizesGroup());
    }
}