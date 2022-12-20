<?php

namespace KALMARS\Migrations;

use KALMARS\Models\KeyValue;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;

/**
 * Class CreateKeyValueTable_0_0_1
 */
class CreateKeyValueTable_0_0_1
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(KeyValue::class);
    }
}
