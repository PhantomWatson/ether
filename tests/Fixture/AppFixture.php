<?php

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class AppFixture extends TestFixture
{
    public $defaultData = [];

    /**
     * Adds a record, using $defaultData for any missing fields
     *
     * @param array $data
     */
    protected function addRecord(array $data)
    {
        $this->records[] = array_merge($this->defaultData, $data);
    }
}
