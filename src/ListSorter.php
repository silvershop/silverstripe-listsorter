<?php

declare(strict_types=1);

namespace SilverShop\ListSorter;

use SilverStripe\Model\List\ArrayList;
use SilverStripe\Model\ModelData;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\ORM\DataList;

/**
 * Control for front-end sorting manipulations
 */
class ListSorter extends ModelData
{
    private HTTPRequest $request;

    /** @var array<string, ListSorterOption> */
    private array $sortOptions = [];

    private ?ListSorterOption $current = null;

    /**
     * @param array<int|string, ListSorterOption|string>|null $options
     */
    public function __construct(HTTPRequest $request, ?array $options = null)
    {
        $this->request = $request;
        if (is_array($options)) {
            $this->setSortOptions($options);
        }
    }

    /**
     * Replace all sort options with a new array
     *
     * @param array<int|string, ListSorterOption|string> $options
     */
    public function setSortOptions(array $options): void
    {
        $this->sortOptions = [];
        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if ($value instanceof ListSorterOption) {
                $this->addSortOption($value);
            } else {
                $this->addSortOption(
                    ListSorterOption::create($key, $value)
                );
            }
        }
    }

    /**
     * Add sort option, and set according to sort request param.
     */
    public function addSortOption(ListSorterOption $option): void
    {
        $this->sortOptions[(string)$option] = $option;
        $requestparam = $this->request->getVar('sort');
        if ((string)$option === $requestparam) {
            $this->current = $option;
        }

        if ((string)$option->getReverseOption() === $requestparam) {
            $this->current = $option->getReverseOption();
        }
    }

    /**
     * Current sort option
     */
    protected function getCurrentOption(): ?ListSorterOption
    {
        return $this->current;
    }

    /**
     * Set the current sort option
     *
     * @param $option
     */
    public function setCurrentOption(ListSorterOption $option): void
    {
        $this->current = $option;
    }

    protected function isCurrent(ListSorterOption $option): bool
    {
        return $option === $this->getCurrentOption();
    }

    /**
     * Get the available sorting options
     *
     * @return ArrayList<ListSorterOption>
     */
    public function getSorts(): ArrayList
    {
        $sorts = ArrayList::create();
        foreach ($this->sortOptions as $option) {
            if ($this->isCurrent($option)) {
                if ($option->isReversable()) {
                    $option = $option->getReverseOption();
                }

                $option = $option->customise(['IsCurrent' => true]);
            }

            $sorts->push($option);
        }

        return $sorts;
    }

    /**
     * Sort the given data list with the current sort
     *
     * @template T of \SilverStripe\ORM\DataObject
     * @param DataList<T> $list
     * @return DataList<T>
     */
    public function sortList(DataList $list): DataList
    {
        if ($current = $this->getCurrentOption()) {
            return $list->sort($current->getSortSet());
        }

        return $list;
    }
}
