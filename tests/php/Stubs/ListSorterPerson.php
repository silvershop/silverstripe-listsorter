<?php

declare(strict_types=1);

namespace SilverShop\ListSorter\Tests\Stubs;

use SilverStripe\Dev\TestOnly;
use SilverStripe\ORM\DataObject;

class ListSorterPerson extends DataObject implements TestOnly
{
    private static string $table_name = 'ListSorterPerson';

    private static array $db = [
        'Title' => 'Varchar',
        'Age' => 'Int'
    ];
}
