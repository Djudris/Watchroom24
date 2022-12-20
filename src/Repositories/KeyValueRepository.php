<?php

namespace KALMARS\Repositories;

use KALMARS\Contracts\KeyValueRepositoryContract;
use KALMARS\Models\KeyValue;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use Plenty\Plugin\Application;

class KeyValueRepository implements KeyValueRepositoryContract
{
    /**
     * Constructor.
     */
    public function __construct()
    {

    }

    /**
     * Set Item
     *
     * @param string $key
     * @param array $value
     * @return KeyValue
     */
    public function setItem($key, $value): KeyValue
    {
        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);
        $app = pluginApp(Application::class);
        $pluginSetId = $app->getPluginSetId();

        $keyValueList = $database->query(KeyValue::class)
            ->where('pluginSetId', '=', $pluginSetId)
            ->where('key', '=', $key)
            ->get();

        if (!empty($keyValueList[0])) {
            $keyValue = $keyValueList[0];
        } else {
            $keyValue = pluginApp(KeyValue::class);
        }

        $keyValue->key = $key;
        $keyValue->value = $value;
        $keyValue->pluginSetId = $pluginSetId;
        $database->save($keyValue);

        return $keyValue;
    }

    /**
     * Get Item
     *
     * @param string $key
     * @return array
     */
    public function getItem($key): array
    {
        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);
        $app = pluginApp(Application::class);
        $pluginSetId = $app->getPluginSetId();

        $keyValueList = $database->query(KeyValue::class)
            ->where('pluginSetId', '=', $pluginSetId)
            ->where('key', '=', $key)
            ->get();

        $result = [];
        if (!empty($keyValueList[0])) {
            $result = $keyValueList[0]->value;
        }

        return is_array($result) ? $result : [];
    }
}
