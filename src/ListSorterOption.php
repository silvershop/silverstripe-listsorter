<?php

namespace SilverShop\ListSorter;

use SilverStripe\Control\HTTP;
use SilverStripe\View\ViewableData;

/**
 * Encapsulate sort option title, sorting SQL,
 * GET parameter key, and reverse option.
 */
class ListSorterOption extends ViewableData
{
    protected $title;
    protected $id;
    protected $sortSet;
    protected $reverseOption;

    public function __construct($title, $sortset, ListSorterOption $reverseOption = null)
    {
        $this->title = $title;
        $this->setID($title);
        $this->sortSet = $sortset;
        if ($reverseOption) {
            $this->setReverseOption($reverseOption);
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getSortSet()
    {
        return $this->sortSet;
    }

    public function setReverseOption(ListSorterOption $option)
    {
        $this->reverseOption = $option;
        if (!$option->isReversable()) {
            if ($this->getID() === $option->getID()) {
                $option->setID((string)$option . "_rev");
            }
            $option->setReverseOption($this);
        }
        return $this;
    }

    public function getReverseOption()
    {
        return $this->reverseOption;
    }

    public function isReversable()
    {
        return (bool)$this->reverseOption;
    }

    public function setID($id)
    {
        $this->id = strtolower(trim($id));
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getLink()
    {
        return $this->generateLink($this->getID());
    }

    /**
     * Helper for creating sort links
     *
     * @param  $id
     * @return string
     */
    private function generateLink($id)
    {
        $url = Http::setGetVar('sort', $id, null, '&');
        //TODO: strip "start" pagination parameter,
        //as most users won't want to remain on paginated page when sorting
        return $url;
    }
}
