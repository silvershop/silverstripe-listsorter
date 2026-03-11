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
    protected string $title;

    protected string $id;

    /** @var string|array<string, string> */
    protected string|array $sortSet;

    protected ?ListSorterOption $reverseOption = null;

    /**
     * @param string|array<string, string> $sortset
     */
    public function __construct(string $title, string|array $sortset, ?ListSorterOption $reverseOption = null)
    {
        $this->title = $title;
        $this->setID($title);
        $this->sortSet = $sortset;
        if ($reverseOption instanceof \SilverShop\ListSorter\ListSorterOption) {
            $this->setReverseOption($reverseOption);
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|array<string, string>
     */
    public function getSortSet(): string|array
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

    public function getReverseOption(): ?ListSorterOption
    {
        return $this->reverseOption;
    }

    public function isReversable(): bool
    {
        return (bool)$this->reverseOption;
    }

    public function setID(string $id): static
    {
        $this->id = strtolower(trim($id));
        return $this;
    }

    public function getID(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getLink(): string
    {
        return $this->generateLink($this->getID());
    }

    /**
     * Helper for creating sort links
     */
    private function generateLink(string $id): string
    {
        //TODO: strip "start" pagination parameter,
        //as most users won't want to remain on paginated page when sorting
        return Http::setGetVar('sort', $id, null, '&');
    }
}
