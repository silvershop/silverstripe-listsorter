# SilverStripe List Sorter

A front-end control for sorting SilverStripe lists easily. The aim of this module is to make sorting lists as simple as it is to use PaginatedList.

## Requirements

 * SilverStripe 3+

## Usage

There are a few ways you can define sort options within an array.

Make a public function on your controller:
```php
function getSorter(){
	$sorts = array(
		'Title', //DB field name only
		'Popularity' => 'Popularity DESC', //map title to sort sql
		'Price' => array("BasePrice" => "ASC"), //map title to data list sort
		new ListSorter_Option("Age", "Created DESC", //object
			new ListSorter_Option("Age", array("Created" => "ASC")) //reverse
		)
	);
	return new ListSorter($this->request,$sorts);
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
