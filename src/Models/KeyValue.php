<?php

namespace KALMARS\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class KeyValue
 *
 * @property int    $id
 * @property int    $pluginSetId
 * @property string $key
 * @property array  $value
 */
class KeyValue extends Model
{
    /**
     * @var int
     */
    public $id = 0;
    public $pluginSetId = 0;
    public $key = '';
    public $value = [];

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'KALMARS::KeyValue';
    }
}
