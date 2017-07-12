<?php

namespace SilverShop\ListSorter\Tests\Stubs;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;

class ListSorterPerson extends DataObject implements TestOnly
{
    private static $db = [
        'Title' => 'Varchar',
        'Age' => 'Int'
    ];
}
