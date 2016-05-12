<?php

namespace AppBundle\Event;


use AppBundle\Entity\ShareSizesGroup;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ShareGroupFiltersUpdated
 * @package AppBundle\Listener
 */
class ShareGroupFiltersUpdated extends Event
{
    /**
     * @var ShareSizesGroup
     */
    protected $shareGroup;

    /**
     * @param ShareSizesGroup $sizesGroup
     */
    public function __construct(ShareSizesGroup $sizesGroup)
    {
        $this->shareGroup = $sizesGroup;
    }

    /**
     * @return ShareSizesGroup
     */
    public function getSizesGroup()
    {
        return $this->shareGroup;
    }
}