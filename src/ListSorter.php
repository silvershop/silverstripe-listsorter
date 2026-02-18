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

    private array $sortOptions = [];

    private $current;

    public function __construct(HTTPRequest $request, $options = null)
    {
        $this->request = $request;
        if (is_array($options)) {
            $this->setSortOptions($options);
        }
    }

    /**
     * Replace all sort options with a new array
     *
     * @param array $options
     */
    public function setSortOptions($options): void
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
     *
     * @return ListSorterOption|null
     */
    protected function getCurrentOption()
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
     */
    public function getSorts()
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
     * @param  DataList $list
     * @return DataList
     */
    public function sortList($list)
    {
        if ($current = $this->getCurrentOption()) {
            return $list->sort($current->getSortSet());
        }

        return $list;
    }
}
