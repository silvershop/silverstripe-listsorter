# SilverStripe List Sorter

[![CI](https://github.com/silvershop/silverstripe-listsorter/actions/workflows/ci.yml/badge.svg)](https://github.com/silvershop/silverstripe-listsorter/actions/workflows/ci.yml)
[![Version](http://img.shields.io/packagist/v/silverstripe/sharedraftcontent.svg?style=flat-square)](https://packagist.org/packages/silvershop/silverstripe-listsorter)
[![License](http://img.shields.io/packagist/l/silverstripe/sharedraftcontent.svg?style=flat-square)](LICENSE.md)

A front-end control for sorting SilverStripe lists easily. The aim of this module is to make sorting lists as simple as it is to use PaginatedList.

## Requirements

 * SilverStripe 4+ or 5+

## Usage

There are a few ways you can define sort options within an array.

Make a public function on your controller:
```php
public function getSorter(){
	$sorts = [
		'Title', //DB field name only
		'Popularity' => 'Popularity DESC', //map title to sort sql
		'Price' => ['BasePrice' => 'ASC'], //map title to data list sort
		ListSorter_Option::create('Age', ['Created' => 'DESC'], //object
			ListSorter_Option::create('Age', ['Created' => 'ASC']) //reverse
		)
	;
	return ListSorter::create($this->request,$sorts);
}
```

Call that function when updating your list:
```php
public function getSortableChildren() {
	$list = $this->Children();
	$list = $this->getSorter()->sortList($list);
	return $list;
}

```

Use my template or roll your own.

```
<% include Sorter %>
<ul>
<% loop SortableChildren %>
	<li>$Title</li>
<% end_loop %>
</ul>
```

## Usage with Silvershop

Silvershop's PageCategoryController comes with some sortings predefined. If you want to define your own sorting possibilities, you can add an Extension to ProductCategory like this:

```php
<?php

namespace MyNamespace\SilverShop\Extensions;

use SilverShop\ListSorter\ListSorter;
use SilverShop\ListSorter\ListSorterOption;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Security;

class ProductCategorySorting extends Extension
{

    public function updateSorter(ListSorter $sorter)
    {

        $basePriceOptionDESC = ListSorterOption::create('BasePrice highest first', ['BasePrice' => 'DESC']);
        $basePriceOptionASC = ListSorterOption::create('BasePrice lowest first', ['BasePrice' => 'ASC']);

        $titleOptionASC = ListSorterOption::create('Title a-z', ['Title' => 'ASC']);
        $titleOptionDESC = ListSorterOption::create('Title z-a', ['Title' => 'DESC']);

        $newestOption = ListSorterOption::create('Newest first', ['Created' => 'DESC']);

        $popularityOption = ListSorterOption::create('Most Popular', ['Popularity' => 'DESC']);

//overwrite all settings
//you can use $sorter->addSortOption($option) if you want to add a sort option

        $sorter->setSortOptions([
            $basePriceOptionASC,
            $basePriceOptionDESC,
            $titleOptionASC,
            $titleOptionDESC,
            $newestOption,
            $popularityOption
        ]);
    }
}

```

Then add this extension to ProductCategoryController in your config:

```yaml
SilverShop\Page\ProductCategoryController:
  extensions:
    - MyNamespace\Silvershop\Extensions\ProductCategorySorting
```
