<?php

class ListSorterTest extends SapphireTest{

	protected static $fixture_file = "listsorter/tests/fixture.yaml";

	protected $extraDataObjects = array(
		'ListSorterTest_Person'
	);
	
	function testSorting() {
		$list = ListSorterTest_Person::get();

		$options = array(
			'Title',
			'Oldest' => 'Age DESC',
			'Youngest' => array("Age" => "ASC"),
			new ListSorter_Option("Age Title", "Age DESC, Title ASC", //object
				new ListSorter_Option("Age Title", array("Age" => "ASC", "Title" => "DESC")) //reverse
			)
		);

		//title asc
		$request = new SS_HTTPRequest('GET','people', array("sort"=>"title"));
		$sorter = new ListSorter($request, $options);
		$list = $sorter->sortList($list);
		$this->assertDOSEquals(array(
			array("Title" => "beth", "Age" => 20),
			array("Title" => "joe", "Age" => 30),
			array("Title" => "sam", "Age" => 10),
			array("Title" => "zoe", "Age" => 10)
		), $list);

		//age + title
		$request = new SS_HTTPRequest('GET','people', array("sort"=>"age+title"));
		$sorter = new ListSorter($request, $options);
		$list = $sorter->sortList($list);
		$this->assertDOSEquals(array(
			array("Title" => "joe", "Age" => 30),
			array("Title" => "beth", "Age" => 20),
			array("Title" => "sam", "Age" => 10),
			array("Title" => "zoe", "Age" => 10)
		), $list);

		//age + title reverse
		$request = new SS_HTTPRequest('GET','people', array("sort"=>"age+title_rev"));
		$sorter = new ListSorter($request, $options);
		$list = $sorter->sortList($list);
		$this->assertDOSEquals(array(
			array("Title" => "zoe", "Age" => 10),
			array("Title" => "sam", "Age" => 10),
			array("Title" => "beth", "Age" => 20),
			array("Title" => "joe", "Age" => 30)
		), $list);
	}

	function testListSorterOption() {
		
		$option = new ListSorter_Option("Age Title", "Age DESC, Title ASC", //object
				new ListSorter_Option("Age Title", array("Age" => "ASC", "Title" => "DESC")) //reverse
		);

		$this->assertEquals("Age Title",$option->Title);
		$this->assertEquals("age title",$option->ID);
		$this->assertEquals("age title", (string)$option);
		$this->assertTrue($option->isReversable());
		$this->assertEquals("dev?sort=age+title",$option->getLink());

		$reverse = $option->getReverseOption();
		$this->assertEquals("Age Title", $reverse->Title);
		$this->assertEquals("age title_rev", $reverse->ID);
		$this->assertEquals("age title_rev", (string)$reverse);
		$this->assertTrue($reverse->isReversable());
		$this->assertEquals("dev?sort=age+title_rev",$reverse->getLink());
		
	}

}

class ListSorterTest_Person extends DataObject implements TestOnly{

	private static $db = array(
		'Title' => 'Varchar',
		'Age' => 'Int'
	);

}