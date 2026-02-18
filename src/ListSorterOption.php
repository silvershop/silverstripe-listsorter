<?php

declare(strict_types=1);

namespace SilverShop\ListSorter;

use SilverStripe\Model\ModelData;
use SilverStripe\Control\HTTP;

/**
 * Encapsulate sort option title, sorting SQL,
 * GET parameter key, and reverse option.
 */
class ListSorterOption extends ModelData
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
        if ($reverseOption instanceof \SilverShop\ListSorter\ListSorterOption) {
            $this->setReverseOption($reverseOption);
        }
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSortSet()
    {
        return $this->sortSet;
    }

    public function setReverseOption(ListSorterOption $option): static
    {
        $this->reverseOption = $option;
        if (!$option->isReversable()) {
            if ($this->getID() === $option->getID()) {
                $option->setID($option . "_rev");
            }

            $option->setReverseOption($this);
        }

        return $this;
    }

    public function getReverseOption()
    {
        return $this->reverseOption;
    }

    public function isReversable(): bool
    {
        return (bool)$this->reverseOption;
    }

    public function setID($id): static
    {
        $this->id = strtolower(trim($id));
        return $this;
    }

    public function getID()
    {
        return $this->id;
    }

    public function __toString(): string
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
        //TODO: strip "start" pagination parameter,
        //as most users won't want to remain on paginated page when sorting
        return Http::setGetVar('sort', $id, null, '&');
    }
}
