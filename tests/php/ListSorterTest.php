<?php

namespace SilverShop\ListSorter\Tests;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Dev\SapphireTest;
use SilverShop\ListSorter\ListSorter;
use SilverShop\ListSorter\ListSorterOption;
use SilverShop\ListSorter\Tests\Stubs\ListSorterPerson;

class ListSorterTest extends SapphireTest
{
    protected static $fixture_file = 'fixture.yaml';

    protected static $extra_dataobjects = [
        ListSorterPerson::class
    ];

    public function testSorting()
    {
        $list = ListSorterPerson::get();

        $options = [
            'Title',
            'Oldest' => 'Age DESC',
            'Youngest' => ['Age' => 'ASC'],
            ListSorterOption::create(
                'Age Title',
                'Age DESC, Title ASC', //object
                ListSorterOption::create('Age Title', ['Age' => 'ASC', 'Title' => 'DESC']) //reverse
            )
        ];

        //title asc
        $request = new HTTPRequest('GET', 'people', ['sort' => 'title']);
        $sorter = ListSorter::create($request, $options);
        $list = $sorter->sortList($list);
        $this->assertListEquals(
            [
                ['Title' => 'beth', 'Age' => 20],
                ['Title' => 'joe', 'Age' => 30],
                ['Title' => 'sam', 'Age' => 10],
                ['Title' => 'zoe', 'Age' => 10]
            ],
            $list
        );

        //age + title
        $request = new HTTPRequest('GET', 'people', ['sort' => 'age+title']);
        $sorter = ListSorter::create($request, $options);
        $list = $sorter->sortList($list);
        $this->assertListEquals(
            [
                ['Title' => 'joe', 'Age' => 30],
                ['Title' => 'beth', 'Age' => 20],
                ['Title' => 'sam', 'Age' => 10],
                ['Title' => 'zoe', 'Age' => 10]
            ],
            $list
        );

        //age + title reverse
        $request = new HTTPRequest('GET', 'people', ['sort' => 'age+title_rev']);
        $sorter = ListSorter::create($request, $options);
        $list = $sorter->sortList($list);
        $this->assertListEquals(
            [
                ['Title' => 'zoe', 'Age' => 10],
                ['Title' => 'sam', 'Age' => 10],
                ['Title' => 'beth', 'Age' => 20],
                ['Title' => 'joe', 'Age' => 30]
            ],
            $list
        );
    }

    public function testListSorterOption()
    {
        $option = ListSorterOption::create(
            'Age Title',
            'Age DESC, Title ASC', //object
            ListSorterOption::create('Age Title', ['Age' => 'ASC', 'Title' => 'DESC']) //reverse
        );

        $this->assertEquals('Age Title', $option->getTitle());
        $this->assertEquals('age title', $option->getID());
        $this->assertEquals('age title', (string)$option);
        $this->assertTrue($option->isReversable());
        $this->assertEquals('?url=%2F&sort=age+title', $option->getLink());

        $reverse = $option->getReverseOption();
        $this->assertEquals('Age Title', $reverse->getTitle());
        $this->assertEquals('age title_rev', $reverse->getID());
        $this->assertEquals('age title_rev', (string)$reverse);
        $this->assertTrue($reverse->isReversable());
        $this->assertEquals('?url=%2F&sort=age+title_rev', $reverse->getLink());
    }
}
